import { Config } from 'ziggy-js';

export interface User {
    id: number;
    name: string;
    email: string;
    email_verified_at?: string;
    role?: string;
    is_admin: boolean;
    street?: string;
    number?: string;
    zipcode?: string;
    city?: string;
    phonenumber?: string;
    bankaccountnumber?: string;
}

export type PageProps<
    T extends Record<string, unknown> = Record<string, unknown>,
> = T & {
    auth: {
        user: User;
    };
    ziggy: Config & { location: string };
};
