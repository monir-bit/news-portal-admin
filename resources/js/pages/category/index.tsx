import React from 'react';
import { DataTable } from '@/components/shared/data-table';
import { CategoryType } from '@/types/models/category-type';
import { BreadcrumbItem, Column } from '@/types';
import { Head, router, usePage } from '@inertiajs/react';
import AppLayout from '@/layouts/app-layout';
import {route} from 'ziggy-js';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import DeleteModal from '@/components/shared/delete-modal';
type PageProps = {
    categories: CategoryType[];
};

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: route('dashboard'),
    },
];

const Index = () => {
    const {categories} = usePage<PageProps>().props
    const [selectedCategory, setSelectedCategory] =
        React.useState<CategoryType | null>(null);
    const [openDeleteModal, setOpenDeleteModal] = React.useState(false);

    const handleDelete = () => {
        if (!selectedCategory) return;
        router.delete(route('category.delete', { id: selectedCategory.id }), {
            onFinish: () => {
                setOpenDeleteModal(false);
                setSelectedCategory(null);
            },
        });
    }


    const columns: Column<CategoryType>[] = [
        {
            key: 'name',
            header: 'Name',
            sortable: true,
            render: (val) => (
                <span className="font-medium text-slate-900">{val}</span>
            ),
        },
        {
            key: 'slug',
            header: 'slug',
        },
        {
            key: 'position',
            header: 'Position',
            sortable: true,
            render: (val) => (
                <span className="rounded-full bg-slate-100 px-2 py-1 text-xs font-bold tracking-wider text-slate-600 uppercase">
                    {val}
                </span>
            ),
        },

        {
            key: 'visible',
            header: 'Visibility',
            sortable: true,
            render: (val) =>
                val ? (
                    <Badge>Yes</Badge>
                ) : (
                    <Badge variant="destructive">No</Badge>
                ),
        },
        {
            key: '',
            header: 'Action',
            sortable: true,
            render: (val, category) => {
                const handleDeleteModal = () => {
                    setOpenDeleteModal(true);
                    setSelectedCategory(category);
                };

                return (
                    <div className="flex gap-2">
                        <Button
                            onClick={() =>
                                router.visit(
                                    route('category.edit', { id: category.id }),
                                )
                            }
                            size="sm"
                        >
                            Edit
                        </Button>
                        <Button
                            onClick={handleDeleteModal}
                            variant="destructive"
                            size="sm"
                        >
                            Delete
                        </Button>
                    </div>
                );
            },
        },
    ];

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Categories" />

            <div className="mb-6 flex items-center justify-between">
                <h2 className="text-2xl font-bold text-slate-900">
                    Categories
                </h2>
                <div className="flex gap-2">
                    <Button
                        onClick={() => router.visit(route('category.create'))}
                    >
                        Create New
                    </Button>
                </div>
            </div>

            <DataTable
                columns={columns}
                data={categories}
                loading={false}
                pageSize={10}
            />

            <DeleteModal
                open={openDeleteModal}
                setOpen={setOpenDeleteModal}
                onConfirm={handleDelete}
            />
        </AppLayout>
    );
};

export default Index;
