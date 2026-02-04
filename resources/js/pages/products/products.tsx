import AppLayout from '@/layouts/app-layout';
import { type BreadcrumbItem, type Product } from '@/types';
import { Head, router } from '@inertiajs/react';
import { useState, useEffect } from 'react';
import { useLang } from '@/hooks/useLang';
import ProductForm from './components/ProductForm';
import DeleteConfirmation from './components/DeleteConfirmation';
import ProductsTable from './components/ProductsTable';
import Pagination from './components/Pagination';
import ProductSearch from './components/ProductSearch';

interface ProductsPageProps {
    products: {
        data: Product[];
        links: { url: string | null; label: string; active: boolean }[];
        from: number;
        to: number;
        total: number;
        current_page: number;
        last_page: number;
    };
    filters: {
        search?: string;
        sort_field?: string;
        sort_direction?: string;
    };
}

export default function Products({ products, filters }: ProductsPageProps) {
    const { __ } = useLang();
    const [isDialogOpen, setIsDialogOpen] = useState(false);
    const [isEditing, setIsEditing] = useState(false);
    const [editingProduct, setEditingProduct] = useState<Product | null>(null);
    const [searchTerm, setSearchTerm] = useState(filters.search || '');
    const [previewUrl, setPreviewUrl] = useState<string | null>(null);

    // State for delete confirmation dialog
    const [isDeleteDialogOpen, setIsDeleteDialogOpen] = useState(false);
    const [deletingProduct, setDeletingProduct] = useState<Product | null>(null);
    const [isDeleting, setIsDeleting] = useState(false);

    // Debounce search input
    useEffect(() => {
        const timeoutId = setTimeout(() => {
            if (searchTerm !== filters.search) {
                router.get(route('products.index'), { search: searchTerm }, {
                    preserveState: true,
                    preserveScroll: true,
                    only: ['products', 'filters']
                });
            }
        }, 300); // 300ms debounce time

        return () => clearTimeout(timeoutId);
    }, [searchTerm]);

    const breadcrumbs: BreadcrumbItem[] = [
        {
            title: __('products.title'),
            href: '/products',
        },
    ];

    const handleEdit = (product: Product) => {
        setIsEditing(true);
        setEditingProduct(product);

        // Set preview URL for existing image
        if (product.has_image) {
            setPreviewUrl(`/user/product/${product.uuid}/image`);
        } else {
            setPreviewUrl(null);
        }

        setIsDialogOpen(true);
    };

    const handleDelete = (product: Product) => {
        setDeletingProduct(product);
        setIsDeleteDialogOpen(true);
    };

    const handleAddClick = () => {
        setIsEditing(false);
        setEditingProduct(null);
        setPreviewUrl(null);
        setIsDialogOpen(true);
    };

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title={__('products.title')} />
            <div className="flex h-full flex-1 flex-col gap-4 rounded-xl p-4 overflow-x-auto">
                {/* Search and Add Product Button */}
                <ProductSearch
                    searchTerm={searchTerm}
                    onSearchChange={setSearchTerm}
                    onAddClick={handleAddClick}
                />

                {/* Product Form Dialog */}
                <ProductForm
                    isOpen={isDialogOpen}
                    onOpenChange={setIsDialogOpen}
                    isEditing={isEditing}
                    editingProduct={editingProduct}
                    onEdit={handleEdit}
                    previewUrl={previewUrl}
                    setPreviewUrl={setPreviewUrl}
                />

                {/* Delete Confirmation Dialog */}
                <DeleteConfirmation
                    isOpen={isDeleteDialogOpen}
                    onOpenChange={setIsDeleteDialogOpen}
                    product={deletingProduct}
                    isDeleting={isDeleting}
                    setIsDeleting={setIsDeleting}
                />

                {/* Products Table */}
                <ProductsTable
                    products={products.data}
                    filters={filters}
                    onEdit={handleEdit}
                    onDelete={handleDelete}
                />

                {/* Pagination */}
                {products.data.length > 0 && (
                    <Pagination
                        from={products.from}
                        to={products.to}
                        total={products.total}
                        links={products.links}
                    />
                )}
            </div>
        </AppLayout>
    );
}
