import FormInput from '@/components/shared/form-input';
import FormSelect from '@/components/shared/form-select';
import { Button } from '@/components/ui/button';
import { useForm } from '@inertiajs/react';
import { route } from 'ziggy-js';

import AppLayout from '@/layouts/app-layout';
import FormSwitch from '@/components/shared/form-switch';

import { CategoryListType } from '@/types/applications/category-list-type';
import { RecursiveCategory } from '@/types/applications/recursive-category-type';
import CategoryTreeView from '@/components/category/category-tree-view';


interface Props {
    categories: CategoryListType[];
    recursiveCategories: RecursiveCategory[];
}

export default function CreateCategory({ categories, recursiveCategories }: Props) {
    const { data, setData, post, processing, reset, errors } = useForm({
        name: '',
        slug: '',
        position: 0,
        visible: true,
        parent_id: null as number | null,
    });

    function submit(e: React.FormEvent) {
        e.preventDefault();
        post(route('category.store'), {
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
