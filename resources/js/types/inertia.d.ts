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
