export type Loan = {
    id: number;
    code: string;
    borrower_name: string;
    borrower_identifier: string | null;
    borrower_department: string | null;
    borrower_contact: string | null;
    borrowed_at: string;
    due_at: string | null;
    notes: string;
    department: { name: string };
    items_count: number;
    returned_count: number;
};
export const conditions: Record<string, string> = {
    good: 'Baik',
    minor_damage: 'Rusak ringan',
    major_damage: 'Rusak berat',
};
export function date(value: string | null) {
    return value
        ? new Intl.DateTimeFormat('id-ID', {
              dateStyle: 'medium',
              timeStyle: 'short',
              timeZone: 'Asia/Makassar',
          }).format(new Date(value)) + ' WITA'
        : '—';
}
export function status(loan: Loan) {
    return loan.returned_count === loan.items_count
        ? 'Selesai'
        : loan.returned_count
          ? 'Sebagian kembali'
          : 'Dipinjam';
}
export function overdue(loan: Loan) {
    return (
        loan.returned_count < loan.items_count &&
        !!loan.due_at &&
        new Date(loan.due_at).getTime() < Date.now()
    );
}
export function localNow() {
    const parts = new Intl.DateTimeFormat('sv-SE', {
        timeZone: 'Asia/Makassar',
        year: 'numeric',
        month: '2-digit',
        day: '2-digit',
        hour: '2-digit',
        minute: '2-digit',
        hourCycle: 'h23',
    }).format(new Date());
    return parts.replace(' ', 'T');
}
