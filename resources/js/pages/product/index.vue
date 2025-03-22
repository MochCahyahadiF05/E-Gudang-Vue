<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, router } from '@inertiajs/vue3';
import { defineProps, ref, getCurrentInstance } from 'vue';

// Mengakses instance Vue untuk menggunakan $notify dari SweetAlert2
const { appContext } = getCurrentInstance()!;
const $notify = appContext.config.globalProperties.$notify;

// Breadcrumbs
const breadcrumbs = [{ title: 'Product', href: '/products' }];

// Props dari Laravel
const props = defineProps({ products: Array });

// Headers untuk Data Table
const search = ref('');
const headers = ref<{ key: string; title: string; sortable: boolean; align?: 'start' | 'end' | 'center' }[]>([
    { key: 'kode_produk', title: 'Kode Produk', sortable: false, align: 'start' },
    { key: 'name', title: 'Name', sortable: false, align: 'start' },
    { key: 'unit', title: 'Unit', sortable: true, align: 'start' },
    { key: 'stock', title: 'Stock', sortable: true, align: 'start' },
    { key: 'purchase_price', title: 'Purchase Price', sortable: true, align: 'start' },
    { key: 'selling_price', title: 'Selling Price', sortable: true, align: 'start' },
    { key: 'description', title: 'Description', sortable: false, align: 'start' },
    { key: 'actions', title: 'Actions', sortable: false, align: 'end' },
]);

const unitOptions = ref([
    { value: 'kg', text: 'Kilogram' },
    { value: 'pcs', text: 'Pieces' },
    { value: 'ltr', text: 'Liter' },
]);

// Form produk
const isModalOpen = ref(false);
const isEditing = ref(false);
const editedProductId = ref<number | null>(null);
const productForm = ref({ kode_produk: '',name: '', unit: '', stock: 0, purchase_price: 0, selling_price: 0, description: '' });

// Fungsi simpan produk (baru/edit)
const saveProduct = () => {
    const formattedData = {
        ...productForm.value,
        stock: Number(productForm.value.stock),
        purchase_price: parseInt(productForm.value.purchase_price.toString().replace(/\./g, ''), 10) || 0,
        selling_price: parseInt(productForm.value.selling_price.toString().replace(/\./g, ''), 10) || 0,
    };

    if (isEditing.value && editedProductId.value !== null) {
        router.put(`/products/${editedProductId.value}`, formattedData, {
            onSuccess: () => {
                resetForm();
                $notify.toast('Produk berhasil diperbarui!', 'success');
            },
            onError: () => {
                $notify.toast('Gagal memperbarui produk!', 'error');
            },
        });
    } else {
        router.post('/products', formattedData, {
            onSuccess: () => {
                resetForm();
                $notify.toast('Produk berhasil ditambahkan!', 'success');
            },
            onError: () => {
                $notify.toast('Gagal menambahkan produk!', 'error');
            },
        });
    }
};

// Fungsi reset form
const resetForm = () => {
    productForm.value = { kode_produk: '',name: '', unit: '', stock: 0, purchase_price: 0, selling_price: 0, description: '' };
    isEditing.value = false;
    editedProductId.value = null;
    isModalOpen.value = false;
};

// Fungsi isi form saat edit
const editProduct = (product: any) => {
    editedProductId.value = product.id;
    productForm.value = {
        kode_produk:product.kode_produk,
        name: product.name,
        unit: product.unit,
        stock: Number(product.stock),
        purchase_price: Number(product.purchase_price),
        selling_price: Number(product.selling_price),
        description: product.description,
    };
    isEditing.value = true;
    isModalOpen.value = true;
};

// Fungsi hapus produk dengan konfirmasi SweetAlert2
const deleteProduct = async (id: number) => {
    const isConfirmed = await $notify.confirm('Yakin ingin menghapus produk ini?');
    if (isConfirmed) {
        router.delete(`/products/${id}`, {
            onSuccess: () => {
                $notify.toast('Produk berhasil dihapus!', 'success');
            },
            onError: () => {
                $notify.toast('Gagal menghapus produk!', 'error');
            },
        });
    }
};

// Format tampilan harga
const formatRupiah = (value: number) => {
    return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(value);
};

const closeModal = () => {
    isModalOpen.value = false;
    resetForm();
};
</script>

<template>
    <Head title="Products" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <v-container>
            <v-row>
                <v-col cols="12">
                    <v-text-field v-model="search" label="Search product..." prepend-inner-icon="mdi-magnify" variant="outlined"></v-text-field>
                </v-col>
            </v-row>

            <!-- Tombol Tambah -->
            <v-row>
                <v-col cols="12">
                    <v-btn color="primary" @click="isModalOpen = true; isEditing = false;">Tambah Produk</v-btn>
                </v-col>
            </v-row>

            <!-- Modal Form Tambah/Edit Produk -->
            <v-dialog v-model="isModalOpen" max-width="500px">
                <v-card>
                    <v-card-title>
                        <span class="text-h5">{{ isEditing ? 'Edit Produk' : 'Tambah Produk' }}</span>
                    </v-card-title>

                    <v-card-text>
                        <v-container>
                            <v-text-field v-model="productForm.kode_produk" label="Kode Produk" required></v-text-field>
                            <v-text-field v-model="productForm.name" label="Nama Produk" required></v-text-field>
                            <v-select v-model="productForm.unit" :items="unitOptions" item-title="text" item-value="value" label="Unit" required></v-select>
                            <v-text-field v-model="productForm.stock" label="Stock" type="number" required></v-text-field>
                            <v-text-field v-model="productForm.purchase_price" label="Harga Beli" type="text" required></v-text-field>
                            <v-text-field v-model="productForm.selling_price" label="Harga Jual" type="text" required></v-text-field>
                            <v-textarea v-model="productForm.description" label="Deskripsi"></v-textarea>
                        </v-container>
                    </v-card-text>

                    <v-card-actions>
                        <v-spacer></v-spacer>
                        <v-btn color="red" text @click="closeModal">Batal</v-btn>
                        <v-btn color="green" text @click="saveProduct">{{ isEditing ? 'Update' : 'Simpan' }}</v-btn>
                    </v-card-actions>
                </v-card>
            </v-dialog>

            <!-- Data Table -->
            <v-row>
                <v-col cols="12">
                    <v-data-table :headers="headers" :items="props.products" :search="search" class="elevation-1">
                        <template v-slot:item.description="{ item }">
                            <v-tooltip location="bottom">
                                <template v-slot:activator="{ props }">
                                    <span v-bind="props" style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 150px; display: inline-block;">
                                        {{ (item as any).description }}
                                    </span>
                                </template>
                                <span>{{ (item as any).description }}</span>
                            </v-tooltip>
                        </template>
                        <template v-slot:item.purchase_price="{ item }">
                            {{ formatRupiah((item as any).purchase_price) }}
                        </template>
                        <template v-slot:item.selling_price="{ item }">
                            {{ formatRupiah((item as any).selling_price) }}
                        </template>
                        <template v-slot:item.actions="{ item }">
                            <v-btn icon size="small" color="blue" @click="editProduct(item)">
                                <v-icon>mdi-pencil</v-icon>
                            </v-btn>
                            <v-btn icon size="small" color="red" @click="deleteProduct((item as any).id)">
                                <v-icon>mdi-delete</v-icon>
                            </v-btn>
                        </template>
                    </v-data-table>
                </v-col>
            </v-row>
        </v-container>
    </AppLayout>
</template>
