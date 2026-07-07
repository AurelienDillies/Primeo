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
import { CreateCourse } from '../create/create-course/create-course';
import { CreateActivitie } from '../create/create-activitie/create-activitie';
import { CreateClasse } from '../create/create-classe/create-classe';
import { UpdateClasse } from '../update/update-classe/update-classe';
import { UpdateCourse } from '../update/update-course/update-course';
import { UpdateActivitie } from '../update/update-activitie/update-activitie';

const ALL_ROLES = ['ROLE_ADMIN', 'ROLE_TEACHER', 'ROLE_PARENT', 'ROLE_STUDENT'];
const TEACHER_STUDENT_ADMIN = ['ROLE_ADMIN', 'ROLE_TEACHER', 'ROLE_STUDENT'];
const PARENT_ADMIN = ['ROLE_ADMIN', 'ROLE_PARENT'];
const TEACHER_ADMIN = ['ROLE_ADMIN', 'ROLE_TEACHER'];
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
    path: 'resume-classes',
    component: Courses,
    canActivate: [roleGuard],
    data: { roles: TEACHER_STUDENT_ADMIN },
    title: 'Résumé des classes'
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
    path: 'nouvelle-classe',
    component: CreateClasse,
    canActivate: [roleGuard],
    data: { roles: TEACHER_ADMIN },
    title: 'Nouvelle classe'
  },
  {
    path: 'nouveau-cours',
    component: CreateCourse,
    canActivate: [roleGuard],
    data: { roles: TEACHER_ADMIN },
    title: 'Nouveau cours'
  },
  {
    path: 'nouvelle-activite',
    component: CreateActivitie,
    canActivate: [roleGuard],
    data: { roles: TEACHER_ADMIN },
    title: 'Nouvelle activité'
  },
  {
    path: 'modifier-classe',
    component: UpdateClasse,
    canActivate: [roleGuard],
    data: { roles: TEACHER_ADMIN },
    title: 'Modifier la classe'
  },
  {
    path: 'modifier-cours',
    component: UpdateCourse,
    canActivate: [roleGuard],
    data: { roles: TEACHER_ADMIN },
    title: 'Modifier le cours'
  },
  {
    path: 'modifier-activite',
    component: UpdateActivitie,
    canActivate: [roleGuard],
    data: { roles: TEACHER_ADMIN },
    title: 'Modifier l\'activité'
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
