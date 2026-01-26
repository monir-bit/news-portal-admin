import { CategoryType } from '@/types/models/category-type';

export type RecursiveCategory = CategoryType & {
    children_recursive: RecursiveCategory[];
};
