import { Routes } from '@angular/router';
import { Login } from './pages/login/login';
import { Register} from './pages/register/register';
import { Profile } from './pages/profile/profile';
import { Classes } from './pages/classes/classes';
import { Courses } from './pages/courses/courses';
import { Activities } from './pages/activities/activities';
import { Progress } from './pages/progress/progress';
import { Messages } from './pages/messages/messages';
import { LegalNotice } from './pages/legal-notice/legal-notice';
import { PrivacyPolicy } from './pages/privacy-policy/privacy-policy';
import { NotFound } from './pages/not-found/not-found';

export const routes: Routes = [
    { path: '', redirectTo: 'connexion', pathMatch: 'full' },
    { path: 'connexion', component: Login },
    { path: 'inscription', component: Register },
    { path: 'profil', component: Profile},
    { path: 'classes', component: Classes },
    { path: 'cours', component: Courses},
    { path: 'activites', component: Activities },
    { path: 'suivi', component: Progress },
    { path: 'messagerie', component: Messages },
    { path: 'mentions-legales', component: LegalNotice },
    { path: 'politique-confidentialite', component: PrivacyPolicy },
    { path: '**', component: NotFound}
];
