import { useMemo, useState } from 'react';
import { Column } from '@/types';

export enum SortDirection {
    ASC = 'asc',
    DESC = 'desc',
    NONE = 'none',
}


interface DataTableProps<T> {
    columns: Column<T>[];
    data: T[];
    loading?: boolean;
    pageSize?: number;
}

export function DataTable<T extends { id: string }>({
    columns,
    data,
    loading = false,
    pageSize = 10,
}: DataTableProps<T>) {
    const [currentPage, setCurrentPage] = useState(1);
    const [searchQuery, setSearchQuery] = useState('');
    const [sortKey, setSortKey] = useState<keyof T | string | null>(null);
    const [sortDirection, setSortDirection] = useState<SortDirection>(
        SortDirection.NONE,
    );

    const filteredData = useMemo(() => {
        return data.filter((item) =>
            Object.values(item).some((val) =>
                String(val).toLowerCase().includes(searchQuery.toLowerCase()),
            ),
        );
    }, [data, searchQuery]);

    const sortedData = useMemo(() => {
        if (!sortKey || sortDirection === SortDirection.NONE)
            return filteredData;

        return [...filteredData].sort((a, b) => {
            const aVal = a[sortKey as keyof T];
            const bVal = b[sortKey as keyof T];

            if (aVal < bVal)
                return sortDirection === SortDirection.ASC ? -1 : 1;
            if (aVal > bVal)
                return sortDirection === SortDirection.ASC ? 1 : -1;
            return 0;
        });
    }, [filteredData, sortKey, sortDirection]);

    const totalPages = Math.ceil(sortedData.length / pageSize);
    const paginatedData = sortedData.slice(
        (currentPage - 1) * pageSize,
        currentPage * pageSize,
    );

    const handleSort = (key: keyof T | string) => {
        if (sortKey === key) {
            if (sortDirection === SortDirection.ASC)
                setSortDirection(SortDirection.DESC);
            else if (sortDirection === SortDirection.DESC) {
                setSortDirection(SortDirection.NONE);
                setSortKey(null);
            }
        } else {
            setSortKey(key);
            setSortDirection(SortDirection.ASC);
        }
        setCurrentPage(1);
    };

    return (
        <div className="flex w-full flex-col overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
            {/* Header / Toolbar */}
            <div className="flex flex-col items-center justify-between gap-4 border-b border-slate-100 p-4 sm:flex-row">
                <div className="relative w-full sm:w-64">
                    <input
                        type="text"
                        placeholder="Search all columns..."
                        className="w-full rounded-lg border border-slate-200 py-2 pr-4 pl-10 text-sm focus:border-transparent focus:ring-2 focus:ring-blue-500 focus:outline-none"
                        value={searchQuery}
                        onChange={(e) => {
                            setSearchQuery(e.target.value);
                            setCurrentPage(1);
                        }}
                    />
                    <span className="absolute top-2.5 left-3 text-slate-400">
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            className="h-4 w-4"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                        >
                            <path
                                strokeLinecap="round"
                                strokeLinejoin="round"
                                strokeWidth={2}
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"
                            />
                        </svg>
                    </span>
                </div>
                <div className="text-sm text-slate-500">
                    Showing{' '}
                    <span className="font-medium text-slate-900">
                        {paginatedData.length}
                    </span>{' '}
                    of{' '}
                    <span className="font-medium text-slate-900">
                        {sortedData.length}
                    </span>{' '}
                    entries
                </div>
            </div>

            {/* Table Area */}
            <div className="flex-grow overflow-x-auto">
                <table className="w-full border-collapse text-left text-sm">
                    <thead>
                        <tr className="border-b border-slate-200 bg-slate-50">
                            {columns.map((col) => (
                                <th
                                    key={String(col.key)}
                                    className={`px-6 py-4 font-semibold text-slate-700 select-none ${col.sortable ? 'cursor-pointer transition-colors hover:bg-slate-100' : ''}`}
                                    onClick={() =>
                                        col.sortable &&
                                        handleSort(col.key as keyof T)
                                    }
                                >
                                    <div className="flex items-center gap-2">
                                        {col.header}
                                        {col.sortable && (
                                            <span className="text-slate-400">
                                                {sortKey === col.key
                                                    ? sortDirection ===
                                                      SortDirection.ASC
                                                        ? '↑'
                                                        : '↓'
                                                    : '↕'}
                                            </span>
                                        )}
                                    </div>
                                </th>
                            ))}
                        </tr>
                    </thead>
                    <tbody className="divide-y divide-slate-100">
                        {loading ? (
                            Array.from({ length: pageSize }).map((_, i) => (
                                <tr key={i} className="animate-pulse">
                                    {columns.map((_, j) => (
                                        <td key={j} className="px-6 py-4">
                                            <div className="h-4 w-full rounded bg-slate-100"></div>
                                        </td>
                                    ))}
                                </tr>
                            ))
                        ) : paginatedData.length > 0 ? (
                            paginatedData.map((item) => (
                                <tr
                                    key={item.id}
                                    className="group transition-colors hover:bg-blue-50/30"
                                >
                                    {columns.map((col) => (
                                        <td
                                            key={String(col.key)}
                                            className="px-6 py-4 whitespace-nowrap text-slate-600"
                                        >
                                            {col.render
                                                ? col.render(
                                                      item[col.key as keyof T],
                                                      item,
                                                  )
                                                : String(
                                                      item[col.key as keyof T],
                                                  )}
                                        </td>
                                    ))}
                                </tr>
                            ))
                        ) : (
                            <tr>
                                <td
                                    colSpan={columns.length}
                                    className="px-6 py-12 text-center text-slate-400"
                                >
                                    No data found matching your query.
                                </td>
                            </tr>
                        )}
                    </tbody>
                </table>
            </div>

            {/* Pagination Footer */}
            <div className="flex items-center justify-between border-t border-slate-200 bg-slate-50 p-4">
                <button
                    disabled={currentPage === 1 || loading}
                    onClick={() => setCurrentPage((p) => Math.max(1, p - 1))}
                    className="rounded border border-slate-300 bg-white px-3 py-1 text-sm transition-colors hover:bg-slate-50 disabled:cursor-not-allowed disabled:opacity-50"
                >
                    Previous
                </button>
                <div className="flex gap-2">
                    {Array.from({ length: Math.min(5, totalPages) }, (_, i) => {
                        // Basic windowed pagination logic
                        let pageNum = i + 1;
                        if (totalPages > 5 && currentPage > 3) {
                            pageNum = currentPage - 2 + i;
                            if (pageNum > totalPages)
                                pageNum = totalPages - (4 - i);
                        }
                        if (pageNum > totalPages) return null;

                        return (
                            <button
                                key={pageNum}
                                onClick={() => setCurrentPage(pageNum)}
                                className={`flex h-8 w-8 items-center justify-center rounded text-sm transition-colors ${
                                    currentPage === pageNum
                                        ? 'bg-blue-600 font-bold text-white'
                                        : 'border border-slate-200 bg-white text-slate-600 hover:border-blue-400'
                                }`}
                            >
                                {pageNum}
                            </button>
                        );
                    })}
                </div>
                <button
                    disabled={
                        currentPage === totalPages ||
                        totalPages === 0 ||
                        loading
                    }
                    onClick={() =>
                        setCurrentPage((p) => Math.min(totalPages, p + 1))
                    }
                    className="rounded border border-slate-300 bg-white px-3 py-1 text-sm transition-colors hover:bg-slate-50 disabled:cursor-not-allowed disabled:opacity-50"
                >
                    Next
                </button>
            </div>
        </div>
    );
}
