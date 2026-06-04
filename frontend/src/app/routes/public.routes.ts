import { Routes } from '@angular/router';
import { LegalNotice } from '../pages/legal-notice/legal-notice';
import { Login } from '../pages/login/login';
import { PrivacyPolicy } from '../pages/privacy-policy/privacy-policy';
import { guestGuard } from '../guards/role.guard';


export const publicRoutes: Routes = [
  { path: 'connexion', component: Login, canActivate: [guestGuard] },
  { path: 'mentions-legales', component: LegalNotice },
  { path: 'politique-confidentialite', component: PrivacyPolicy },
];
