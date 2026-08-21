import { CommonModule } from '@angular/common';
import { ChangeDetectorRef, Component, OnInit } from '@angular/core';
import { FormsModule } from '@angular/forms';
import { UserService, UserProfileUpdate } from '../../services/user-service';
import { User as UserModel } from '../../models/user.model';

@Component({
  selector: 'app-user',
  standalone: true,
  imports: [CommonModule, FormsModule],
  templateUrl: './user.html',
  styleUrl: './user.css',
})
export class User implements OnInit {
  users: UserModel[] = [];
  loading = false;
  errorMessage = '';
  successMessage = '';

  editingUserId: number | null = null;
  editFirstName = '';
  editLastName = '';
  editEmail = '';
  editPassword = '';
  editSubmitted = false;

  constructor(
    private readonly userService: UserService,
    private readonly cdr: ChangeDetectorRef
  ) {}

  ngOnInit(): void {
    if (!this.userService.hasAnyRole(['ROLE_ADMIN'])) {
      this.users = [];
      this.loading = false;
      return;
    }

    this.loadUsers();
  }

  loadUsers(): void {
    this.loading = true;
    this.errorMessage = '';

    this.userService.getUsers().subscribe({
      next: (users) => {
        this.users = users;
        this.loading = false;
        this.cdr.detectChanges();
      },
      error: () => {
        this.loading = false;
        this.errorMessage = 'Impossible de charger les utilisateurs.';
        this.cdr.detectChanges();
      },
    });
  }

  startEdit(user: UserModel): void {
    this.successMessage = '';
    this.errorMessage = '';
    this.editingUserId = user.id;
    this.editFirstName = user.first_name;
    this.editLastName = user.last_name;
    this.editEmail = user.email;
    this.editPassword = '';
    this.editSubmitted = false;
  }

  cancelEdit(): void {
    this.editingUserId = null;
    this.editPassword = '';
    this.editSubmitted = false;
  }

  saveEdit(userId: number): void {
    this.editSubmitted = true;
    const firstName = this.editFirstName.trim();
    const lastName = this.editLastName.trim();
    const email = this.editEmail.trim();

    if (!firstName || !lastName || !email || firstName.length > 50 || lastName.length > 50 || email.length > 180) {
      return;
    }

    if (!this.isValidEmail(email)) {
      return;
    }

    if (this.editPassword && this.editPassword.length < 6) {
      return;
    }

    const payload: UserProfileUpdate = {
      first_name: firstName,
      last_name: lastName,
      email,
    };

    if (this.editPassword.trim()) {
      payload.password = this.editPassword.trim();
    }

    this.userService.updateById(userId, payload).subscribe({
      next: (updatedUser) => {
        this.users = this.users.map((user) =>
          user.id === userId ? updatedUser : user
        );
        this.cancelEdit();
        this.errorMessage = '';
        this.successMessage = 'Utilisateur mis à jour avec succès.';
        this.cdr.detectChanges();
      },
      error: (error) => {
        this.successMessage = '';
        this.errorMessage = error?.error?.error ?? 'Échec de la mise à jour utilisateur.';
        this.cdr.detectChanges();
      },
    });
  }

  deleteUser(user: UserModel): void {
    this.successMessage = '';
    this.errorMessage = '';

    const currentUserId = this.userService.getUserId();
    if (currentUserId === user.id) {
      this.errorMessage = 'Vous ne pouvez pas supprimer votre propre compte.';
      return;
    }

    const confirmed = window.confirm(
      `Confirmer la suppression de ${user.first_name} ${user.last_name} ?`
    );

    if (!confirmed) {
      return;
    }

    this.userService.deleteById(user.id).subscribe({
      next: () => {
        this.users = this.users.filter((item) => item.id !== user.id);
        this.cancelEdit();
        this.successMessage = 'Utilisateur supprimé avec succès.';
        this.cdr.detectChanges();
      },
      error: (error) => {
        this.errorMessage = error?.error?.error ?? 'Échec de la suppression utilisateur.';
        this.cdr.detectChanges();
      },
    });
  }

  displayRole(roles: string[] = []): string {
    if (!roles.length) {
      return 'ROLE_USER';
    }

    return roles.join(', ');
  }

  isValidEmail(email: string): boolean {
    return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
  }
}
