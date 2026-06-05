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
    path: 'cours-details',
    component: CourseDetails,
    canActivate: [roleGuard],
    data: { roles: TEACHER_STUDENT_ADMIN }
  },
  {
    path: 'activites',
    component: Activities,
    canActivate: [roleGuard],
    data: { roles: TEACHER_STUDENT_ADMIN },
  },
  {
    path: 'activite-details',
    component: ActivityDetails,
    canActivate: [roleGuard],
    data: { roles: TEACHER_STUDENT_ADMIN }
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
  {
    path: 'utilisateurs',
    component: Users,
    canActivate: [roleGuard],
    data: { roles: ADMIN },
  },
  {
    path: 'inscription',
    component: Register,
    canActivate: [roleGuard],
    data: { roles: ADMIN },
  }
];
