import { useState } from 'react';
import { RecursiveCategory } from '@/types/applications/recursive-category-type';

/** -------- Tree Item -------- */
function CategoryTree({ node }: { node: RecursiveCategory }) {
    const [open, setOpen] = useState(true);
    const hasChildren = node.children_recursive?.length > 0;

    return (
        <div className="ml-4">
            <div className="flex items-center gap-2 py-1">
                {hasChildren && (
                    <button
                        onClick={() => setOpen(!open)}
                        className="flex h-5 w-5 items-center justify-center rounded bg-gray-200 text-xs hover:bg-gray-300"
                    >
                        {open ? '−' : '+'}
                    </button>
                )}

                {!hasChildren && <span className="w-5" />}

                <span className="text-sm font-medium text-gray-800">
                    {node.name}
                </span>
            </div>

            {open && hasChildren && (
                <div className="ml-2 border-l border-gray-300 pl-2">
                    {node.children_recursive.map((child) => (
                        <CategoryTree key={child.id} node={child} />
                    ))}
                </div>
            )}
        </div>
    );
}

/** -------- Tree View -------- */
export default function CategoryTreeView({ data }: { data: RecursiveCategory[] }) {
    return (
        <div className="max-w-md rounded-lg bg-white p-4">
            <h2 className="mb-3 text-lg font-semibold text-gray-900">
                📂 Category Tree
            </h2>

            {data.map((item) => (
                <CategoryTree key={item.id} node={item} />
            ))}
        </div>
    );
}
