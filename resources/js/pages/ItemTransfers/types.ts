export type Transfer = {
    id: number;
    code: string;
    type: 'room' | 'department';
    transferred_at: string;
    notes: string;
    from_department_name: string | null;
    to_department_name: string | null;
    from_room: { name: string } | null;
    to_room: { name: string } | null;
    institution: { name: string; address?: string | null };
    recorder?: { name: string };
    items_count?: number;
};
