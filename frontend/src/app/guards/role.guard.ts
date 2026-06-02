import { inject } from '@angular/core';
import { CanActivateFn, Router } from '@angular/router';
import { UserService } from '../services/user-service';

export const roleGuard: CanActivateFn = (route) => {
  const userService = inject(UserService);
  const router = inject(Router);

  if (!userService.isAuthenticated()) {
    return router.createUrlTree(['/connexion']);
  }

  const allowedRoles = route.data?.['roles'] as string[] | undefined;
  if (!allowedRoles || allowedRoles.length === 0) {
    return true;
  }

  const userRoles = userService.getUserRoles();
  const isAllowed = allowedRoles.some((role) => userRoles.includes(role));

  return isAllowed ? true : router.createUrlTree(['/profil']);
};
