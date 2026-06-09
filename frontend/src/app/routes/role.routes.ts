import { Routes } from '@angular/router';
import { roleGuard } from '../guards/role.guard';
import { Activities } from '../pages/activities/activities';
import { Childrens } from '../pages/childrens/childrens';
import { Classes } from '../pages/classes/classes';
import { Courses } from '../pages/courses/courses';
import { Messages } from '../pages/messages/messages';
import { Profile } from '../pages/profile/profile';
import { Progress } from '../pages/progress/progress';
import { Users } from '../pages/users/users';
import { CourseDetails } from '../pages/course-details/course-details';
import { ActivityDetails } from '../pages/activity-details/activity-details';
import { Register } from '../pages/register/register';

const ALL_ROLES = ['ROLE_ADMIN', 'ROLE_TEACHER', 'ROLE_PARENT', 'ROLE_STUDENT'];
const TEACHER_STUDENT_ADMIN = ['ROLE_ADMIN', 'ROLE_TEACHER', 'ROLE_STUDENT'];
const PARENT_ADMIN = ['ROLE_ADMIN', 'ROLE_PARENT'];
const ADMIN = ['ROLE_ADMIN'];

export const roleRoutes: Routes = [
  {
    path: 'profil',
    component: Profile,
    canActivate: [roleGuard],
    data: { roles: ALL_ROLES },
    title: 'Profil'
  },
  {
    path: 'messagerie',
    component: Messages,
    canActivate: [roleGuard],
    data: { roles: ALL_ROLES },
    title: 'Messagerie'
  },
  {
    path: 'classes',
    component: Classes,
    canActivate: [roleGuard],
    data: { roles: TEACHER_STUDENT_ADMIN },
    title: 'Classes'
  },
  {
    path: 'cours',
    component: Courses,
    canActivate: [roleGuard],
    data: { roles: TEACHER_STUDENT_ADMIN },
    title: 'Cours'
  },
  {
    path: 'cours-details',
    component: CourseDetails,
    canActivate: [roleGuard],
    data: { roles: TEACHER_STUDENT_ADMIN },
    title: 'Détails du cours'
  },
  {
    path: 'activites',
    component: Activities,
    canActivate: [roleGuard],
    data: { roles: TEACHER_STUDENT_ADMIN },
    title: 'Activités'
  },
  {
    path: 'activite-details',
    component: ActivityDetails,
    canActivate: [roleGuard],
    data: { roles: TEACHER_STUDENT_ADMIN },
    title: 'Détails de l\'activité'
  },
  {
    path: 'suivi',
    component: Progress,
    canActivate: [roleGuard],
    data: { roles: ALL_ROLES },
    title: 'Suivi'
  },
  {
    path: 'enfants',
    component: Childrens,
    canActivate: [roleGuard],
    data: { roles: PARENT_ADMIN },
    title: 'Enfants'
  },
  {
    path: 'utilisateurs',
    component: Users,
    canActivate: [roleGuard],
    data: { roles: ADMIN },
    title: 'Utilisateurs'
  },
  {
    path: 'inscription',
    component: Register,
    canActivate: [roleGuard],
    data: { roles: ADMIN },
    title: 'Inscription'
  }
];
