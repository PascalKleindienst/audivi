import { PageProps } from '@inertiajs/core';

export interface User {
    name: string;
    email: string;
    email_verified_at: string | null;
    profile_photo_url: string;
    profile_photo_path: string | null;
    two_factor_enabled: boolean;
    updated_at: string | null;
    created_at: string;
}

export interface PaginatedDataCollection<T> {
    data: T[];
    links: Array<{
        url: string | null;
        label: string;
        active: boolean;
    }>;
    meta: {
        current_page: number;
        first_page_url: string;
        from: number;
        last_page: number;
        last_page_url: string;
        next_page_url: string | null;
        path: string;
        per_page: number;
        prev_page_url: string | null;
        to: number;
        total: number;
    };
}

export interface SharedProps extends PageProps {
    auth: {
        user: User;
    };
    canResetPassword: boolean;
    errorBags: unknown;
    jetstream: {
        canCreateTeams: boolean;
        canManageTwoFactorAuthentication: boolean;
        canUpdatePassword: boolean;
        canUpdateProfileInformation: boolean;
        hasAccountDeletionFeatures: boolean;
        hasApiFeatures: boolean;
        hasEmailVerification: boolean;
        hasTeamFeatures: boolean;
        hasTermsAndPrivacyPolicyFeature: boolean;
        managesProfilePhotos: boolean;
    };
    status?: string;
}
