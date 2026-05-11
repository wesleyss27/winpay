export type UserRole = 'admin' | 'produtor' | 'assistente';

export interface User {
  id: number;
  name: string;
  email: string;
  role: UserRole;
  produtor_id: number | null;
  active: boolean;
  created_at: string;
  updated_at: string;
}

export interface AuthResponse {
  access_token: string;
  refresh_token: string;
  user: User;
}
