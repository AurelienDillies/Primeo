import { Routes } from '@angular/router';
import { roleGuard } from '../guards/role.guard';
import { Activities } from '../pages/activities/activities';
import { Childrens } from '../pages/childrens/childrens';
import { Classes } from '../pages/classes/classes';
import { Courses } from '../pages/courses/courses';
import { Messages } from '../pages/messages/messages';
import { Profile } from '../pages/profile/profile';
import { Progress } from '../pages/progress/progress';

const ALL_ROLES = ['ROLE_ADMIN', 'ROLE_TEACHER', 'ROLE_PARENT', 'ROLE_STUDENT'];
const TEACHER_STUDENT_ADMIN = ['ROLE_ADMIN', 'ROLE_TEACHER', 'ROLE_STUDENT'];
const PARENT_ADMIN = ['ROLE_ADMIN', 'ROLE_PARENT'];

export const roleRoutes: Routes = [
  {
    path: 'profil',
    component: Profile,
    canActivate: [roleGuard],
    data: { roles: ALL_ROLES },
  },
  {
    path: 'messagerie',
    component: Messages,
    canActivate: [roleGuard],
    data: { roles: ALL_ROLES },
  },
  {
    path: 'classes',
    component: Classes,
    canActivate: [roleGuard],
    data: { roles: TEACHER_STUDENT_ADMIN },
  },
  {
    path: 'cours',
    component: Courses,
    canActivate: [roleGuard],
    data: { roles: TEACHER_STUDENT_ADMIN },
  },
  {
    path: 'activites',
    component: Activities,
    canActivate: [roleGuard],
    data: { roles: TEACHER_STUDENT_ADMIN },
  },
  {
    path: 'suivi',
    component: Progress,
    canActivate: [roleGuard],
    data: { roles: ALL_ROLES },
  },
  {
    path: 'enfants',
    component: Childrens,
    canActivate: [roleGuard],
    data: { roles: PARENT_ADMIN },
  },
];
