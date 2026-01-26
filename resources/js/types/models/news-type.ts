import { NewsDetails } from '@/types/models/news-details';
import { FileType } from '@/types';

export type NewsType = {
    id: string;
    shoulder: string | null;
    title: string;
    ticker: string | null;
    sort_description: string;
    category_id: number | null;
    order: number | null;
    proofreader: number | null;
    image: FileType;
    type: string;
    timeline_id: number | null;
    published: boolean;
    latest: boolean;
    news_marquee: boolean;
    live_news: boolean;
    is_visible_shoulder: boolean;
    is_visible_ticker: boolean;
    date: string;
    created_by: number | null;
    updated_by: number | null;
    created_at: string;
    updated_at: string;
    deleted_at: string | null;
    category?: {
        id: number;
        name: string;
    };
    details?: NewsDetails | null;
};

