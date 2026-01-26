export type SectionLayoutOrderSequenceType = number[]

export type AllSectionLayoutType = {
    id: string;
    name: string;
    slug: string;
    max_news: number;
    order_sequence: SectionLayoutOrderSequenceType;
};
