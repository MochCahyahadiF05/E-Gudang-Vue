<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, router } from '@inertiajs/vue3';
import { defineProps, ref, getCurrentInstance } from 'vue';

// Mengakses instance Vue untuk menggunakan $notify dari SweetAlert2
const { appContext } = getCurrentInstance()!;
const $notify = appContext.config.globalProperties.$notify;

const breadcrumbs = [{ title: 'Supplier', href: '/suppliers' }];

// Props dari Laravel
const props = defineProps({ suppliers: Array });

//search
const search = ref('');
// Form produk
const isModalOpen = ref(false);
const isEditing = ref(false);
const editedSupplierId = ref<number | null>(null);
const supplierForm = ref({ kode_supplier:'',name:'',contact:'',address:''});

const headers = ref<{ key: string; title: string; sortable: boolean; align?: 'start' | 'end' | 'center' }[]>([
    { key: 'kode_supplier', title: 'Kode Supplier', sortable: false, align: 'start' },
    { key: 'name', title: 'Name', sortable: true, align: 'start' },
    { key: 'contact', title: 'Contact', sortable: true, align: 'start' },
    { key: 'address', title: 'Address', sortable: true, align: 'start' },
    { key: 'actions', title: 'Actions', sortable: false, align: 'end' },
]);

// Fungsi simpan produk (baru/edit)
const saveSupplier = () => {
    const formattedData = {
        ...supplierForm.value
    }

    if (isEditing.value && editedSupplierId.value !== null) {
        router.put(`/suppliers/${editedSupplierId.value}`,formattedData,{
            onSuccess: () => {
                resetForm();
                $notify.toast('Supplier berhasil diperbarui!','success');
            },
            onError: () => {
                $notify.toast('Gagal memperbarui supplier!','error');
            },
        });
    } else {
        router.post('/suppliers', formattedData,{
            onSuccess: () => {
                resetForm();
                $notify.toast('Supplier berhasil ditambahkan!','success');
            },
            onError: () => {
                $notify.toast('Gagal menambahkan supplier!','error');
            },
        });
    }
}
console.log(props.suppliers)
const editSupplier = (suplier: any) => {
    editedSupplierId.value = suplier.id;
    supplierForm.value = {
        kode_supplier: suplier.kode_supllier,
        name: suplier.name,
        contact: suplier.contact,
        address: suplier.address,
    };
    isEditing.value = true;
    isModalOpen.value = true;
}

const deleteSupplier = async (id:number) => {
    const isConfirmed = await $notify.confirm('Yakin ingin menghapus produk ini?');

    if (isConfirmed) {
        router.delete(`/suppliers/${id}`, {
            onSuccess: () => {
                $notify.toast('Supplier berhasil dihapus!', 'success');
            },
            onError: () => {
                $notify.toast('Gagal menghapus supplier!', 'error');
            },
        });
    }
}

// Fungsi reset form
const resetForm = () => {
    supplierForm.value = { kode_supplier:'',name:'',contact:'',address:''};
    isEditing.value = false;
    editedSupplierId.value = null;
    isModalOpen.value = false;
};
const closeModal = () => {
    isModalOpen.value = false;
    resetForm();
};
</script>

<template>
    <Head title="Suppliers" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <v-container>
            <v-row>
                <v-col cols="12">
                    <v-text-field v-model="search" label="Search Supplier..." prepend-inner-icon="mdi-magnify" variant="outlined"></v-text-field>
                </v-col>
            </v-row>
            <!-- Tombol Tambah -->
            <v-row>
                <v-col cols="12">
                    <v-btn color="primary" @click="isModalOpen = true; isEditing = false;">Tambah Supplier</v-btn>
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
                            <v-text-field v-model="supplierForm.kode_supplier" label="Kode Supplier" required></v-text-field>
                            <v-text-field v-model="supplierForm.name" label="Nama Supplier" required></v-text-field>
                            <v-text-field v-model="supplierForm.contact" label="Contact" required></v-text-field>
                            <v-textarea v-model="supplierForm.address" label="Address"></v-textarea>
                        </v-container>
                    </v-card-text>

                    <v-card-actions>
                        <v-spacer></v-spacer>
                        <v-btn color="red" text @click="closeModal">Batal</v-btn>
                        <v-btn color="green" text @click="saveSupplier">{{ isEditing ? 'Update' : 'Simpan' }}</v-btn>
                    </v-card-actions>
                </v-card>
            </v-dialog>

            <!-- Data Table -->
             <v-row>
                <v-col cols="12">
                    <v-data-table :headers="headers" :items="props.suppliers" :search="search" class="elevation-1">
                        <template v-slot:item.actions="{ item }">
                            <v-btn icon size="small" color="blue" @click="editSupplier(item)">
                                <v-icon>mdi-pencil</v-icon>
                            </v-btn>
                            <v-btn icon size="small" color="red" @click="deleteSupplier((item as any).id)">
                                <v-icon>mdi-delete</v-icon>
                            </v-btn>
                        </template>
                    </v-data-table>
                </v-col>
             </v-row>


        </v-container>
    </AppLayout>
</template>