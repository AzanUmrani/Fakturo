import { Button } from '@/components/ui/button';
import { Dialog, DialogContent, DialogDescription, DialogFooter, DialogHeader, DialogTitle, DialogTrigger } from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { type Product } from '@/types';
import { useForm } from '@inertiajs/react';
import { ImageIcon, PlusIcon, UploadIcon } from 'lucide-react';
import { useEffect, useRef, useState } from 'react';
import { useLang } from '@/hooks/useLang';

interface ProductFormProps {
    isOpen: boolean;
    onOpenChange: (open: boolean) => void;
    isEditing: boolean;
    editingProduct: Product | null;
    onEdit: (product: Product) => void;
    previewUrl: string | null;
    setPreviewUrl: (url: string | null) => void;
}

export default function ProductForm({
    isOpen,
    onOpenChange,
    isEditing,
    editingProduct,
    onEdit,
    previewUrl,
    setPreviewUrl
}: ProductFormProps) {
    const { __ } = useLang();
    const fileInputRef = useRef<HTMLInputElement>(null);
    const [isSubmitting, setIsSubmitting] = useState(false);

    const { data, setData, post, put, processing, errors, reset } = useForm({
        name: '',
        type: '',
        description: '',
        price: '0',
        taxRate: '0',
        discount: '0',
        unit: '',
        sku: '',
        weight: '',
        has_image: false,
        image: null as File | null,
    });

    // Reset form when dialog opens/closes or editing state changes
    useEffect(() => {
        if (!isOpen) {
            reset();
        }
    }, [isOpen, reset]);

    // Set form data when editing a product
    useEffect(() => {
        if (isEditing && editingProduct) {
            setData({
                name: editingProduct.name,
                type: editingProduct.type,
                description: editingProduct.description || '',
                price: editingProduct.price.toString(),
                taxRate: editingProduct.taxRate.toString(),
                discount: '0', // editingProduct.discount ? editingProduct.discount.toString() : '',
                unit: editingProduct.unit,
                sku: editingProduct.sku || '',
                weight: editingProduct.weight ? editingProduct.weight.toString() : '',
                has_image: editingProduct.has_image || false,
                image: null, // Reset image field when editing
            });
        }
    }, [isEditing, editingProduct, setData]);

    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault();
        setIsSubmitting(true);

        // Use FormData for file uploads
        const formData = new FormData();

        // Add all form fields to FormData
        Object.keys(data).forEach(key => {
            // Skip null values and the image field (handled separately)
            if (data[key] !== null && key !== 'image') {
                formData.append(key, data[key]);
            }
        });

        // Add image file if it exists
        if (data.image) {
            formData.append('image', data.image);
        }

        const options = {
            forceFormData: true,
            onSuccess: (page: any) => {
                setIsSubmitting(false);
                onOpenChange(false);
                reset();
            },
            onError: (errors: any) => {
                setIsSubmitting(false);
                console.error('Form submission error:', errors);
            },
            onFinish: () => {
                setIsSubmitting(false);
            }
        };

        // if (isEditing && editingProduct) {
        //     put(route('products.update', editingProduct.id), formData, options);
        // } else {
        //     post(route('products.store'), formData, options);
        // }

        if (isEditing && editingProduct) {
            formData.append('_method', 'PUT');
            post(route('productss.update', editingProduct.id), formData);
            window.location.reload();
        } else {
            post(route('products.store'), formData);
            window.location.reload();
        }

    };

    // Handle file selection for image upload
    const handleFileChange = (e: React.ChangeEvent<HTMLInputElement>) => {
        const file = e.target.files?.[0] || null;
        setData('image', file);

        // Create preview URL for the selected image
        if (file) {
            const url = URL.createObjectURL(file);
            setPreviewUrl(url);
            setData('has_image', true);
        } else {
            setPreviewUrl(null);
            setData('has_image', false);
        }
    };

    // Clean up preview URL when component unmounts
    useEffect(() => {
        return () => {
            if (previewUrl && previewUrl.startsWith('blob:')) {
                URL.revokeObjectURL(previewUrl);
            }
        };
    }, [previewUrl]);

    // Close modal when form submission completes successfully (no errors)
    useEffect(() => {
        if (isSubmitting && !processing && Object.keys(errors).length === 0) {
            setIsSubmitting(false);
            onOpenChange(false);
            reset();
        }
    }, [processing, errors, isSubmitting, onOpenChange, reset]);

    return (
        <Dialog open={isOpen} onOpenChange={onOpenChange}>
            <DialogContent className="sm:max-w-[600px]">
                <DialogHeader>
                    <DialogTitle>{isEditing ? __('products.edit_product') : __('products.form.title_add')}</DialogTitle>
                    <DialogDescription>
                        {isEditing
                            ? __('products.form.description_edit')
                            : __('products.form.description_add')}
                    </DialogDescription>
                </DialogHeader>
                <div className="max-h-[60vh] overflow-y-auto pr-4">
                    <form onSubmit={handleSubmit}>
                        <div className="grid gap-4 py-4">
                            <div className="grid grid-cols-2 gap-4">
                                <div className="grid gap-2">
                                    <Label htmlFor="name">{__('products.form.name')} *</Label>
                                    <Input
                                        id="name"
                                        value={data.name}
                                        onChange={(e) => setData('name', e.target.value)}
                                        required
                                    />
                                    {errors.name && <p className="text-sm text-red-500">{errors.name}</p>}
                                </div>
                                <div className="grid gap-2">
                                    <Label htmlFor="type">{__('products.form.type')} *</Label>
                                    <Select
                                        value={data.type}
                                        onValueChange={(value) => setData('type', value)}
                                        required
                                    >
                                        <SelectTrigger id="type" className="w-full">
                                            <SelectValue placeholder={__('products.form.type')} />
                                        </SelectTrigger>
                                        <SelectContent>
                                            <SelectItem value="product">{__('products.form.types.product')}</SelectItem>
                                            <SelectItem value="service">{__('products.form.types.service')}</SelectItem>
                                            <SelectItem value="gift">{__('products.form.types.gift')}</SelectItem>
                                        </SelectContent>
                                    </Select>
                                    {errors.type && <p className="text-sm text-red-500">{errors.type}</p>}
                                </div>
                            </div>

                            <div className="grid gap-2">
                                <Label htmlFor="description">{__('products.form.description')}</Label>
                                <Input
                                    id="description"
                                    value={data.description}
                                    onChange={(e) => setData('description', e.target.value)}
                                />
                                {errors.description && <p className="text-sm text-red-500">{errors.description}</p>}
                            </div>

                            <div className="grid grid-cols-2 gap-4">
                                <div className="grid gap-2">
                                    <Label htmlFor="price">{__('products.form.price')} *</Label>
                                    <Input
                                        id="price"
                                        type="number"
                                        step="0.01"
                                        min="0"
                                        value={data.price}
                                        onChange={(e) => setData('price', e.target.value)}
                                        required
                                    />
                                    {errors.price && <p className="text-sm text-red-500">{errors.price}</p>}
                                </div>
                                <div className="grid gap-2">
                                    <Label htmlFor="taxRate">{__('products.form.taxRate')} *</Label>
                                    <Input
                                        id="taxRate"
                                        type="number"
                                        step="0.01"
                                        min="0"
                                        value={data.taxRate}
                                        onChange={(e) => setData('taxRate', e.target.value)}
                                        required
                                    />
                                    {errors.taxRate && <p className="text-sm text-red-500">{errors.taxRate}</p>}
                                </div>
                            </div>

                            <div className="grid grid-cols-2 gap-4">
                                <div className="grid gap-2">
                                    <Label htmlFor="discount">{__('products.form.discount')}</Label>
                                    <Input
                                        id="discount"
                                        type="number"
                                        step="0.01"
                                        min="0"
                                        value={data.discount}
                                        onChange={(e) => setData('discount', e.target.value)}
                                    />
                                    {errors.discount && <p className="text-sm text-red-500">{errors.discount}</p>}
                                </div>
                                <div className="grid gap-2">
                                    <Label htmlFor="unit">{__('products.form.unit')}</Label>
                                    <Input
                                        id="unit"
                                        value={data.unit}
                                        onChange={(e) => setData('unit', e.target.value)}
                                    />
                                    {errors.unit && <p className="text-sm text-red-500">{errors.unit}</p>}
                                </div>
                            </div>

                            <div className="grid grid-cols-2 gap-4">
                                <div className="grid gap-2">
                                    <Label htmlFor="sku">{__('products.form.sku')}</Label>
                                    <Input
                                        id="sku"
                                        value={data.sku}
                                        onChange={(e) => setData('sku', e.target.value)}
                                    />
                                    {errors.sku && <p className="text-sm text-red-500">{errors.sku}</p>}
                                </div>
                                <div className="grid gap-2">
                                    <Label htmlFor="weight">{__('products.form.weight')}</Label>
                                    <Input
                                        id="weight"
                                        type="number"
                                        step="0.01"
                                        min="0"
                                        value={data.weight}
                                        onChange={(e) => setData('weight', e.target.value)}
                                    />
                                    {errors.weight && <p className="text-sm text-red-500">{errors.weight}</p>}
                                </div>
                            </div>

                            {/* Image Upload */}
                            <div className="grid gap-2">
                                <Label htmlFor="image">{__('products.form.has_image')}</Label>
                                <div className="flex items-center gap-4">
                                    <div className="relative w-32 h-32 border rounded-md overflow-hidden flex items-center justify-center bg-gray-50">
                                        {previewUrl ? (
                                            <img
                                                src={previewUrl}
                                                alt="Product"
                                                className="max-w-full max-h-full object-contain"
                                            />
                                        ) : (
                                            <ImageIcon className="h-12 w-12 text-gray-300" />
                                        )}
                                    </div>
                                    <div className="flex flex-col gap-2">
                                        <input
                                            type="file"
                                            id="image"
                                            ref={fileInputRef}
                                            className="hidden"
                                            accept="image/*"
                                            onChange={handleFileChange}
                                        />
                                        <Button
                                            type="button"
                                            variant="outline"
                                            onClick={() => fileInputRef.current?.click()}
                                        >
                                            <UploadIcon className="h-4 w-4 mr-2" />
                                            {__('products.form.upload_image')}
                                        </Button>
                                        {previewUrl && (
                                            <Button
                                                type="button"
                                                variant="destructive"
                                                onClick={() => {
                                                    setPreviewUrl(null);
                                                    setData('image', null);
                                                    setData('has_image', false);
                                                    if (fileInputRef.current) {
                                                        fileInputRef.current.value = '';
                                                    }
                                                }}
                                            >
                                                {__('products.form.remove_image')}
                                            </Button>
                                        )}
                                    </div>
                                </div>
                                {errors.image && <p className="text-sm text-red-500">{errors.image}</p>}
                            </div>
                        </div>
                        <DialogFooter>
                            <Button
                                type="button"
                                variant="outline"
                                onClick={() => onOpenChange(false)}
                                disabled={processing || isSubmitting}
                            >
                                {__('Cancel')}
                            </Button>
                            {/* <Button type="submit" disabled={processing || isSubmitting}>
                                {isEditing ? __('products.form.submit_edit') : __('products.form.submit_add')}
                            </Button> */}
                            <Button type="submit">
                                {isEditing ? __('products.form.submit_edit') : __('products.form.submit_add')}
                            </Button>
                        </DialogFooter>
                    </form>
                </div>
            </DialogContent>
        </Dialog>
    );
}
