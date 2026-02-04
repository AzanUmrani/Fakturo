import { Button } from '@/components/ui/button';
import { type Product } from '@/types';
import { router } from '@inertiajs/react';
import { ImageIcon } from 'lucide-react';
import { useLang } from '@/hooks/useLang';

interface ProductsTableProps {
    products: Product[];
    filters: {
        search?: string;
        sort_field?: string;
        sort_direction?: string;
    };
    onEdit: (product: Product) => void;
    onDelete: (product: Product) => void;
}

export default function ProductsTable({
    products,
    filters,
    onEdit,
    onDelete
}: ProductsTableProps) {
    const { __ } = useLang();

    const handleSort = (field: string) => {
        router.get(route('products.index'), {
            sort_field: field,
            sort_direction: filters.sort_field === field && filters.sort_direction === 'asc' ? 'desc' : 'asc',
            search: filters.search
        }, {
            preserveState: true,
            preserveScroll: true,
            only: ['products', 'filters']
        });
    };

    return (
        <div className="rounded-md border">
            <div className="relative w-full overflow-auto">
                <table className="w-full caption-bottom text-sm">
                    <thead className="[&_tr]:border-b">
                        <tr className="border-b transition-colors hover:bg-muted/50 data-[state=selected]:bg-muted">
                            <th className="h-12 px-4 text-left align-middle font-medium text-muted-foreground">
                                {/* Image column */}
                                <span className="font-medium text-muted-foreground">
                                    {__('products.table.image')}
                                </span>
                            </th>
                            <th className="h-12 px-4 text-left align-middle font-medium text-muted-foreground">
                                <Button
                                    variant="ghost"
                                    className="p-0 font-medium text-muted-foreground hover:text-foreground"
                                    onClick={() => handleSort('name')}
                                >
                                    {__('products.table.name')}
                                    {filters.sort_field === 'name' && (
                                        <span className="ml-2">
                                            {filters.sort_direction === 'asc' ? '↑' : '↓'}
                                        </span>
                                    )}
                                </Button>
                            </th>
                            <th className="h-12 px-4 text-left align-middle font-medium text-muted-foreground">
                                <Button
                                    variant="ghost"
                                    className="p-0 font-medium text-muted-foreground hover:text-foreground"
                                    onClick={() => handleSort('type')}
                                >
                                    {__('products.table.type')}
                                    {filters.sort_field === 'type' && (
                                        <span className="ml-2">
                                            {filters.sort_direction === 'asc' ? '↑' : '↓'}
                                        </span>
                                    )}
                                </Button>
                            </th>
                            <th className="h-12 px-4 text-left align-middle font-medium text-muted-foreground">
                                <Button
                                    variant="ghost"
                                    className="p-0 font-medium text-muted-foreground hover:text-foreground"
                                    onClick={() => handleSort('price')}
                                >
                                    {__('products.table.price')}
                                    {filters.sort_field === 'price' && (
                                        <span className="ml-2">
                                            {filters.sort_direction === 'asc' ? '↑' : '↓'}
                                        </span>
                                    )}
                                </Button>
                            </th>
                            <th className="h-12 px-4 text-left align-middle font-medium text-muted-foreground">
                                {__('products.table.sku')}
                            </th>
                            <th className="h-12 px-4 text-left align-middle font-medium text-muted-foreground">
                                {__('products.table.actions')}
                            </th>
                        </tr>
                    </thead>
                    <tbody className="[&_tr:last-child]:border-0">
                        {products.length === 0 ? (
                            <tr>
                                <td colSpan={6} className="p-4 text-center text-muted-foreground">
                                    {__('products.no_products')}
                                </td>
                            </tr>
                        ) : (
                            products.map((product) => (
                                <tr key={product.id} className="border-b transition-colors hover:bg-muted/50 data-[state=selected]:bg-muted">
                                    <td className="p-4 align-middle">
                                        <div className="w-12 h-12 relative rounded-md overflow-hidden border flex items-center justify-center bg-gray-50">
                                            {product.has_image ? (
                                                <img
                                                    src={`/user/product/${product.uuid}/image`}
                                                    alt={product.name}
                                                    className="max-w-full max-h-full object-contain"
                                                />
                                            ) : (
                                                <ImageIcon className="h-6 w-6 text-gray-300" />
                                            )}
                                        </div>
                                    </td>
                                    <td className="p-4 align-middle">{product.name}</td>
                                    <td className="p-4 align-middle">{product.type}</td>
                                    <td className="p-4 align-middle">{product.price}</td>
                                    <td className="p-4 align-middle">{product.sku}</td>
                                    <td className="p-4 align-middle">
                                        <div className="flex gap-2">
                                            <Button variant="outline" size="sm" onClick={() => onEdit(product)}>
                                                {__('products.buttons.edit')}
                                            </Button>
                                            <Button variant="destructive" size="sm" onClick={() => onDelete(product)}>
                                                {__('products.buttons.delete')}
                                            </Button>
                                        </div>
                                    </td>
                                </tr>
                            ))
                        )}
                    </tbody>
                </table>
            </div>
        </div>
    );
}
