import React, { useState } from 'react';
import { TagType } from '@/types/models/tag-type';
import { BreadcrumbItem } from '@/types';
import { Head, router, usePage, useForm } from '@inertiajs/react';
import AppLayout from '@/layouts/app-layout';
import { route } from 'ziggy-js';
import { Button } from '@/components/ui/button';
import DeleteModal from '@/components/shared/delete-modal';
import FormInput from '@/components/shared/form-input';
import { X } from 'lucide-react';

type PageProps = {
    tags: TagType[];
};

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: route('dashboard'),
    },
    {
        title: 'Tags',
        href: route('tag.index'),
    },
];

const Index = () => {
    const { tags } = usePage<PageProps>().props;
    const [selectedTag, setSelectedTag] = useState<TagType | null>(null);
    const [openDeleteModal, setOpenDeleteModal] = useState(false);
    const [filterText, setFilterText] = useState('');

    const { data, setData, post, processing, reset, errors } = useForm({
        name: '',
    });

    const handleDelete = () => {
        if (!selectedTag) return;
        router.delete(route('tag.delete', { id: selectedTag.id }), {
            onFinish: () => {
                setOpenDeleteModal(false);
                setSelectedTag(null);
            },
        });
    };

    const handleDeleteModal = (tag: TagType) => {
        setOpenDeleteModal(true);
        setSelectedTag(tag);
    };

    function submit(e: React.FormEvent) {
        e.preventDefault();
        post(route('tag.store'), {
            onSuccess: () => {
                reset();
            },
        });
    }

    // Filter tags based on search text
    const filteredTags = tags.filter((tag) =>
        tag.name.toLowerCase().includes(filterText.toLowerCase())
    );

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Tags" />

            <div className="mb-6">
                <h2 className="text-2xl font-bold text-slate-900">Tags Management</h2>
            </div>

            <div className="grid gap-6">
                {/* Create Tag Form */}
                <div className="rounded-lg border bg-white p-6 shadow-sm">
                    <h3 className="mb-4 text-lg font-semibold text-slate-900">
                        Add New Tag
                    </h3>
                    <form onSubmit={submit} className="flex gap-3">
                        <div className="flex-1">
                            <FormInput
                                placeholder="Enter tag name (e.g., Technology, Sports, Politics)"
                                value={data.name}
                                error={errors.name}
                                onChange={(e) => setData('name', e.target.value)}
                            />
                        </div>
                        <Button type="submit" disabled={processing}>
                            Add Tag
                        </Button>
                    </form>
                </div>

                {/* Filter Input */}
                <div className="rounded-lg border bg-white p-6 shadow-sm">
                    <div className="mb-4 flex items-center justify-between">
                        <h3 className="text-lg font-semibold text-slate-900">
                            All Tags ({filteredTags.length})
                        </h3>
                        <div className="w-64">
                            <FormInput
                                placeholder="Filter tags..."
                                value={filterText}
                                onChange={(e) => setFilterText(e.target.value)}
                            />
                        </div>
                    </div>

                    {/* Tags Display */}
                    {filteredTags.length === 0 ? (
                        <div className="py-12 text-center">
                            <p className="text-slate-500">
                                {filterText
                                    ? 'No tags found matching your filter.'
                                    : 'No tags yet. Create your first tag above.'}
                            </p>
                        </div>
                    ) : (
                        <div className="flex flex-wrap gap-3">
                            {filteredTags.map((tag) => (
                                <div
                                    key={tag.id}
                                    className="group relative inline-flex items-center gap-2 rounded-lg border-2 border-slate-200 bg-slate-50 px-4 py-2 transition-all hover:border-slate-300 hover:bg-slate-100"
                                >
                                    <span className="text-sm font-medium text-slate-700">
                                        {tag.name}
                                    </span>
                                    <button
                                        onClick={() => handleDeleteModal(tag)}
                                        className="flex h-5 w-5 items-center justify-center rounded-full bg-red-100 text-red-600 opacity-70 transition-all hover:bg-red-200 hover:opacity-100 group-hover:opacity-100"
                                        title="Delete tag"
                                        type="button"
                                    >
                                        <X className="h-3 w-3" />
                                    </button>
                                </div>
                            ))}
                        </div>
                    )}
                </div>
            </div>

            <DeleteModal
                open={openDeleteModal}
                setOpen={setOpenDeleteModal}
                onConfirm={handleDelete}
            />
        </AppLayout>
    );
};

export default Index;

