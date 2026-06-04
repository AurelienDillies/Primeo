import { Student } from "./student.model";

export interface User {
  id: number;
  last_name: string;
  first_name: string;
  email: string;
  password: string;
  roles: string[];
  created_at: Date;
  children: Student[];
}