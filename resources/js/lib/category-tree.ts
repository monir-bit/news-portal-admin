
export type CategoryNode = {
    id: number;
    name: string;
    children?: CategoryNode[];
};

export function flattenTree(
    nodes: CategoryNode[],
    level = 0,
): { label: string; value: number }[] {
    let result: { label: string; value: number }[] = [];

    nodes.forEach((node) => {
        result.push({
            label: `${'—'.repeat(level)} ${node.name}`,
            value: node.id,
        });

        if (node.children && node.children.length > 0) {
            result = result.concat(flattenTree(node.children, level + 1));
        }
    });

    return result;
}
