import React from 'react';
import { DataTable } from '@/components/shared/data-table';
import { NewsType } from '@/types/models/news-type';
import { BreadcrumbItem, Column } from '@/types';
import { Head, router, usePage } from '@inertiajs/react';
import AppLayout from '@/layouts/app-layout';
import { route } from 'ziggy-js';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import DeleteModal from '@/components/shared/delete-modal';

type PageProps = {
    news: NewsType[];
};

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: route('dashboard'),
    },
];

const Index = () => {
    const { news } = usePage<PageProps>().props;
    const [selectedNews, setSelectedNews] = React.useState<NewsType | null>(null);
    const [openDeleteModal, setOpenDeleteModal] = React.useState(false);

    const handleDelete = () => {
        if (!selectedNews) return;
        router.delete(route('news.delete', { id: selectedNews.id }), {
            onFinish: () => {
                setOpenDeleteModal(false);
                setSelectedNews(null);
            },
        });
    };

    const columns: Column<NewsType>[] = [
        {
            key: 'title',
            header: 'Title',
            sortable: true,
            render: (val) => (
                <span className="font-medium text-slate-900">{val}</span>
            ),
        },
        {
            key: 'category',
            header: 'Category',
            render: (val) => (
                <span>{val?.name || 'N/A'}</span>
            ),
        },
        {
            key: 'type',
            header: 'Type',
            sortable: true,
            render: (val) => (
                <span className="rounded-full bg-slate-100 px-2 py-1 text-xs font-bold tracking-wider text-slate-600 uppercase">
                    {val}
                </span>
            ),
        },
        {
            key: 'published',
            header: 'Published',
            sortable: true,
            render: (val) =>
                val ? (
                    <Badge>Yes</Badge>
                ) : (
                    <Badge variant="destructive">No</Badge>
                ),
        },
        {
            key: 'latest',
            header: 'Latest',
            sortable: true,
            render: (val) =>
                val ? (
                    <Badge>Yes</Badge>
                ) : (
                    <Badge variant="secondary">No</Badge>
                ),
        },
        {
            key: 'date',
            header: 'Date',
            sortable: true,
            render: (val) => new Date(val).toLocaleDateString(),
        },
        {
            key: '',
            header: 'Action',
            render: (val, news) => {
                const handleDeleteModal = () => {
                    setOpenDeleteModal(true);
                    setSelectedNews(news);
                };

                return (
                    <div className="flex gap-2">
                        <Button
                            onClick={() =>
                                router.visit(
                                    route('news.edit', { id: news.id })
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
            <Head title="News" />

            <div className="mb-6 flex items-center justify-between">
                <h2 className="text-2xl font-bold text-slate-900">News</h2>
                <div className="flex gap-2">
                    <Button onClick={() => router.visit(route('news.create'))}>
                        Create New
                    </Button>
                </div>
            </div>

            <DataTable
                columns={columns}
                data={news}
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

