import { PageProps } from '@inertiajs/core';

export interface SharedProps extends PageProps {
    auth: {
        user: {
            name: string;
            email: string;
            profile_photo_url: string;
        };
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
