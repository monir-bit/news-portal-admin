import FormInput from '@/components/shared/form-input';
import FormSelect from '@/components/shared/form-select';
import { Button } from '@/components/ui/button';
import { useForm } from '@inertiajs/react';
import { route } from 'ziggy-js';

import AppLayout from '@/layouts/app-layout';
import FormSwitch from '@/components/shared/form-switch';
import CategoryTreeView from '@/components/category/category-tree-view';
import { CategoryListType } from '@/types/applications/category-list-type';
import { CategoryType } from '@/types/models/category-type';
import { RecursiveCategory } from '@/types/applications/recursive-category-type';

interface Props {
    categories: CategoryListType[];
    recursiveCategories: RecursiveCategory[];
    category: CategoryType;
}


export default function EditCategory({ categories, recursiveCategories, category }: Props) {
    const { data, setData, post, processing, errors, reset } = useForm({
        name: category?.name ?? '',
        slug: category?.slug ?? '',
        position: category?.position ?? 0,
        visible: category?.visible ?? true,
        parent_id: category?.parent_id ??  null as number | null,
    });

    function submit(e: React.FormEvent) {
        e.preventDefault();
        post(route('category.update', {id: category.id}), {
            onFinish: () => {
                reset()
            }
        });
    }

    return (
        <AppLayout>
            <div className="grid gap-5 grid-cols-5">
                <div className="border p-4 col-span-3 space-y-6">
                    <h1 className="text-xl font-semibold">Create Category</h1>

                    <form onSubmit={submit} className="space-y-4">
                        <FormInput
                            label="Category Name"
                            required
                            value={data.name}
                            error={errors.name}
                            onChange={(e) => setData('name', e.target.value)}
                        />

                        <FormInput
                            label="Slug"
                            required
                            value={data.slug}
                            error={errors.slug}
                            onChange={(e) => setData('slug', e.target.value)}
                        />

                        <FormInput
                            label="Position"
                            type="number"
                            value={data.position}
                            error={errors.position}
                            onChange={(e) =>
                                setData('position', Number(e.target.value))
                            }
                        />

                        <FormSelect
                            label="Parent Category"
                            placeholder="Root Category"
                            value={data.parent_id}
                            error={errors.parent_id}
                            options={categories.map((p) => ({
                                label: p.name,
                                value: p.id,
                            }))}
                            onChange={(value) =>
                                setData('parent_id', Number(value))
                            }
                        />

                        <FormSwitch
                            label="Visible"
                            description="Show this category on frontend"
                            checked={data.visible}
                            error={errors.visible}
                            onChange={(checked) => setData('visible', checked)}
                        />

                        <Button type="submit" disabled={processing}>
                            Save Category
                        </Button>
                    </form>
                </div>
                <div className='col-span-2 border'>
                    <CategoryTreeView data={recursiveCategories} />
                </div>
            </div>
        </AppLayout>
    );
}
