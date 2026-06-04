import { Component, ElementRef, HostListener } from '@angular/core';
import { RouterLink, RouterLinkActive } from '@angular/router';
import { UserService } from '../../services/user-service';

@Component({
  selector: 'app-header',
  imports: [RouterLink, RouterLinkActive],
  templateUrl: './header.html',
  styleUrl: './header.css',
})
export class Header {
  isMenuOpen = false;

  readonly rolesForClasses = ['ROLE_ADMIN', 'ROLE_TEACHER', 'ROLE_STUDENT'];
  readonly rolesForChildren = ['ROLE_ADMIN', 'ROLE_PARENT'];
  readonly rolesForAdmin = ['ROLE_ADMIN'];

  constructor(
    private host: ElementRef<HTMLElement>,
    public userService: UserService
  ) {}

  toggleMenu(event: Event): void {
    event.stopPropagation();
    this.isMenuOpen = !this.isMenuOpen;
  }

  closeMenu(): void {
    this.isMenuOpen = false;
  }

  logout(): void {
    this.closeMenu();
    this.userService.logout();
  }

  @HostListener('document:click', ['$event'])
  onDocumentClick(event: MouseEvent): void {
    if (!this.isMenuOpen) {
      return;
    }

    const target = event.target as Node | null;
    if (target && this.host.nativeElement.contains(target)) {
      return;
    }

    this.closeMenu();
  }

  @HostListener('document:keydown.escape')
  onEscape(): void {
    this.closeMenu();
  }
}
