import { TrackItem } from '@/Composables/usePlayer';
import type { PageProps } from '@inertiajs/core';
import type { Config } from 'ziggy-js';
import BreadcrumbItemData = App.Data.BreadcrumbItemData;

export interface Auth {
    user: User;
}

export interface User {
    name: string;
    email: string;
    is_admin: boolean;
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
    auth: Auth;
    ziggy: Config & { location: string };
    sidebarOpen: boolean;
    canResetPassword: boolean;
    errorBags: unknown;
    breadcrumbs: BreadcrumbItemData[];
    trackList: TrackItem[];
    jetstream: {
        flash?: {
            banner?: string;
            bannerStyle?: string;
        };
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
