import { Routes } from '@angular/router';
import { NotFound } from './pages/not-found/not-found';
import { publicRoutes } from './routes/public.routes';
import { roleRoutes } from './routes/role.routes';

export const routes: Routes = [
    { path: '', redirectTo: 'connexion', pathMatch: 'full' },
    ...publicRoutes,
    ...roleRoutes,
    { path: '**', component: NotFound },
];
