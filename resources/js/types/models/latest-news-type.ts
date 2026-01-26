import { NewsType } from '@/types/models/news-type';

export type LatestNewsType = {
    id: string;
    position: number;
    news_id: number;
    created_at: string;
    updated_at: string;
    news?: NewsType
}
