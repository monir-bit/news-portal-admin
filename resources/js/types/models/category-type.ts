export type CategoryType = {
    id: string;
    name: string;
    slug: string;
    position: number;
    visible: boolean;
    parent_id: number | null;
    created_at: string;
    updated_at: string;
};
