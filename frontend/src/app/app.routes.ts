import { Routes } from '@angular/router';
import { Login } from './pages/login/login';
import { Inscription } from './pages/inscription/inscription';

export const routes: Routes = [
    { path: "", component: Login },
    { path: 'inscription', component: Inscription }
];
