declare namespace App.Data {
    export type AudioBookData = {
        id: number;
        title: string;
        path: string;
        subtitle: string | null;
        volume: number | null;
        description?: string | null;
        rating: number | null;
        cover: string | null;
        published_at: string | null;
        created_at: string | null;
        updated_at: string | null;
        authors?: Array<App.Data.AuthorData>;
    };
    export type AuthorData = {
        id: number;
        name: string;
        image?: string | null;
        description?: string | null;
        link?: string | null;
        created_at?: string | null;
        updated_at?: string | null;
    };
}
