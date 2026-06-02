export interface User {
  id: number;
  lastname: string;
  firstname: string;
  email: string;
  password: string;
  role: string;
  created_at: Date;
}