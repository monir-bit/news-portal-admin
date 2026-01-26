import { DataTable } from '@/components/shared/data-table';
import DeleteModal from '@/components/shared/delete-modal';
import { Button } from '@/components/ui/button';
import AppLayout from '@/layouts/app-layout';
import { BreadcrumbItem, Column } from '@/types';
import { Head, router, usePage } from '@inertiajs/react';
import { useState } from 'react';
import { route } from 'ziggy-js';
import { MarqueNewsType } from '@/types/models/marque-news-type';

type PageProps = {
    marqueNews: MarqueNewsType[];
};

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: route('dashboard'),
    },
];

const Index = () => {
    const { marqueNews } = usePage<PageProps>().props;
    const [selectedMarqueNews, setSelectedMarqueNews] = useState<MarqueNewsType | null>(null);
    const [openDeleteModal, setOpenDeleteModal] = useState(false);

    const handleDelete = () => {
        if (!selectedMarqueNews) return;
        router.delete(
            route('marque-news.delete', { id: selectedMarqueNews.id }),
            {
                onFinish: () => {
                    setOpenDeleteModal(false);
                    setSelectedMarqueNews(null);
                },
            },
        );
    };

    const columns: Column<MarqueNewsType>[] = [
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
            key: 'news',
            header: 'News Title',
            sortable: true,
            render: (val) => (
                <span className="font-medium text-slate-900">
                    {val?.title || 'N/A'}
                </span>
            ),
        },
        {
            key: '',
            header: 'Action',
            sortable: false,
            render: (val, marqueNews) => {
                const handleDeleteModal = () => {
                    setOpenDeleteModal(true);
                    setSelectedMarqueNews(marqueNews);
                };

                return (
                    <div className="flex gap-2">
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
            <Head title="Marque News" />

            <div className="mb-6 flex items-center justify-between">
                <h2 className="text-2xl font-bold text-slate-900">
                    Marque News
                </h2>
            </div>

            <DataTable
                columns={columns}
                data={marqueNews}
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
