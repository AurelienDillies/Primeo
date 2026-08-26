import { Component, Input, OnChanges, SimpleChanges } from '@angular/core';
import { FormsModule, NgForm } from '@angular/forms';
import { Router } from '@angular/router';
import { UserProfileUpdate, UserService } from '../../services/user-service';
import { User } from '../../models/user.model';

@Component({
  selector: 'app-forms-profile',
  standalone: true,
  imports: [FormsModule],
  templateUrl: './forms-profile.html',
  styleUrl: './forms-profile.css',
})
export class FormsProfile implements OnChanges {

  @Input() user: User | null = null;

  first_name = '';
  last_name = '';
  email = '';
  password = '';
  errorMessage = '';

  constructor(
    private userService: UserService,
    private router: Router
  ) {}

  ngOnChanges(changes: SimpleChanges): void {
    if (changes['user']?.currentValue) {
      const user = changes['user'].currentValue as User;

      this.first_name = user.first_name;
      this.last_name = user.last_name;
      this.email = user.email;
    }
  }

  onSubmit(form: NgForm): void {
    if (form.invalid) {
      this.errorMessage = 'Veuillez corriger les champs signalés avant de continuer.';
      form.control.markAllAsTouched();
      return;
    }

    this.errorMessage = '';
    const payload: UserProfileUpdate = {
      first_name: this.first_name,
      last_name: this.last_name,
      email: this.email,
    };

    if (this.password.trim()) {
      payload.password = this.password;
    }

    this.userService.update(payload).subscribe({
      next: () => {
        console.log('Profil mis à jour');
        this.router.navigate(['/profil']);
      },
      error: (err) => {
        this.errorMessage = err?.error?.error ?? 'Impossible de modifier le profil.';
      }
    });
  }
}