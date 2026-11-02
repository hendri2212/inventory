export type User = {
    id: number;
    name: string;
    email: string;
    role: 'superadmin' | 'admin' | 'operator' | 'peminjam';
    institution_id: number | null;
    department_id: number | null;
    avatar?: string;
    email_verified_at: string | null;
    created_at: string;
    updated_at: string;
    [key: string]: unknown;
};

export type Auth = {
    user: User;
};
