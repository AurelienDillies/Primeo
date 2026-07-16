export interface AcademicActivity {
  id: number;
  activityType: string;
  activityTitle: string;
  activityDescription: string | null;
  activityDate: string;
}

export interface AcademicProgress {
  id: number;
  progressPercent: number;
  progressGrade: string | null;
  student?: {
    id: number;
    last_name: string;
    first_name: string;
    email: string;
  } | null;
}

export interface AcademicProgressCard extends AcademicProgress {
  courseTitle: string;
  className: string;
}

export interface AcademicCourse {
  id: number;
  courseTitle: string;
  courseDescription: string;
  courseResourcefile: string | null;
  courseVideoUrl: string | null;
  progresses?: AcademicProgress[];
  activities: AcademicActivity[];
}

export interface AcademicClass {
  id: number;
  className: string;
  classDescription: string | null;
  courses: AcademicCourse[];
}

export interface AcademicUser {
  roles: string[];
  first_name?: string;
  last_name?: string;
  email?: string;
  enrollmentDate?: string | null;
  classes?: AcademicClass[];
  teachingClasses?: AcademicClass[];
  progresses?: AcademicProgress[];
}

export interface ParentChildSummary {
  id: number;
  first_name: string;
  last_name: string;
  email: string;
  enrollmentDate: string | null;
  classesCount: number;
  progressCount: number;
}

export interface ParentChildDetail extends AcademicUser {
  id: number;
  first_name: string;
  last_name: string;
  email: string;
  enrollmentDate: string | null;
  classes: AcademicClass[];
}