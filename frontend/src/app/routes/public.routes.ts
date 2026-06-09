import { Routes } from '@angular/router';
import { LegalNotice } from '../pages/legal-notice/legal-notice';
import { Login } from '../pages/login/login';
import { PrivacyPolicy } from '../pages/privacy-policy/privacy-policy';
import { guestGuard } from '../guards/role.guard';


export const publicRoutes: Routes = [
  { path: 'connexion', component: Login, canActivate: [guestGuard], title: 'Connexion' },
  { path: 'mentions-legales', component: LegalNotice, title: 'Mentions légales' },
  { path: 'politique-confidentialite', component: PrivacyPolicy, title: 'Politique de confidentialité' },
];
