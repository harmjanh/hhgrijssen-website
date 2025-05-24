export interface User {
    id: number;
    name: string;
    email: string;
    date_of_birth?: string;
    street?: string;
    number?: string;
    zipcode?: string;
    city?: string;
    phonenumber?: string;
    bankaccountnumber?: string;
    email_verified_at?: string | null;
}
