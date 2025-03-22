<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, router } from '@inertiajs/vue3';
import { defineProps, ref, getCurrentInstance } from 'vue';

// Mengakses instance Vue untuk menggunakan $notify dari SweetAlert2
const { appContext } = getCurrentInstance()!;
const $notify = appContext.config.globalProperties.$notify;

const breadcrumbs = [{ title: 'Customer', href: '/customers' }];

// Props dari Laravel
const props = defineProps({ customers: Array });

//search
const search = ref('');
// Form produk
const isModalOpen = ref(false);
const isEditing = ref(false);
const editedCustomerId = ref<number | null>(null);
const customerForm = ref({ full_name:'',contact:'',address:''});

const headers = ref<{ key: string; title: string; sortable: boolean; align?: 'start' | 'end' | 'center' }[]>([
    { key: 'full_name', title: 'Full Name', sortable: true, align: 'start' },
    { key: 'contact', title: 'Contact', sortable: true, align: 'start' },
    { key: 'address', title: 'Address', sortable: true, align: 'start' },
    { key: 'actions', title: 'Actions', sortable: false, align: 'end' },
]);

// Fungsi simpan produk (baru/edit)
const saveCustomer = () => {
    const formattedData = {
        ...customerForm.value
    }

    if (isEditing.value && editedCustomerId.value !== null) {
        router.put(`/customers/${editedCustomerId.value}`,formattedData,{
            onSuccess: () => {
                resetForm();
                $notify.toast('Customer berhasil diperbarui!','success');
            },
            onError: () => {
                $notify.toast('Gagal memperbarui Customer!','error');
            },
        });
    } else {
        router.post('/customers', formattedData,{
            onSuccess: () => {
                resetForm();
                $notify.toast('Customer berhasil ditambahkan!','success');
            },
            onError: () => {
                $notify.toast('Gagal menambahkan Customer!','error');
            },
        });
    }
}
console.log(props.customers)
const editCustomer = (suplier: any) => {
    editedCustomerId.value = suplier.id;
    customerForm.value = {
        full_name: suplier.full_name,
        contact: suplier.contact,
        address: suplier.address,
    };
    isEditing.value = true;
    isModalOpen.value = true;
}

const deleteCustomer = async (id:number) => {
    const isConfirmed = await $notify.confirm('Yakin ingin menghapus produk ini?');

    if (isConfirmed) {
        router.delete(`/customers/${id}`, {
            onSuccess: () => {
                $notify.toast('Customer berhasil dihapus!', 'success');
            },
            onError: () => {
                $notify.toast('Gagal menghapus Customer!', 'error');
            },
        });
    }
}

// Fungsi reset form
const resetForm = () => {
    customerForm.value = { full_name:'',contact:'',address:''};
    isEditing.value = false;
    editedCustomerId.value = null;
    isModalOpen.value = false;
};
const closeModal = () => {
    isModalOpen.value = false;
    resetForm();
};
</script>

<template>
    <Head title="Customers" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <v-container>
            <v-row>
                <v-col cols="12">
                    <v-text-field v-model="search" label="Search Customer..." prepend-inner-icon="mdi-magnify" variant="outlined"></v-text-field>
                </v-col>
            </v-row>
            <!-- Tombol Tambah -->
            <v-row>
                <v-col cols="12">
                    <v-btn color="primary" @click="isModalOpen = true; isEditing = false;">Tambah customers</v-btn>
                </v-col>
            </v-row>

            <!-- Modal Form Tambah/Edit Produk -->
            <v-dialog v-model="isModalOpen" max-width="500px">
                <v-card>
                    <v-card-title>
                        <span class="text-h5">{{ isEditing ? 'Edit Customer' : 'Tambah Customer' }}</span>
                    </v-card-title>

                    <v-card-text>
                        <v-container>
                            <v-text-field v-model="customerForm.full_name" label="Nama Lengkap" required></v-text-field>
                            <v-text-field v-model="customerForm.contact" label="Contact" required></v-text-field>
                            <v-textarea v-model="customerForm.address" label="Address"></v-textarea>
                        </v-container>
                    </v-card-text>

                    <v-card-actions>
                        <v-spacer></v-spacer>
                        <v-btn color="red" text @click="closeModal">Batal</v-btn>
                        <v-btn color="green" text @click="saveCustomer">{{ isEditing ? 'Update' : 'Simpan' }}</v-btn>
                    </v-card-actions>
                </v-card>
            </v-dialog>

            <!-- Data Table -->
             <v-row>
                <v-col cols="12">
                    <v-data-table :headers="headers" :items="props.customers" :search="search" class="elevation-1">
                        <template v-slot:item.actions="{ item }">
                            <v-btn icon size="small" color="blue" @click="editCustomer(item)">
                                <v-icon>mdi-pencil</v-icon>
                            </v-btn>
                            <v-btn icon size="small" color="red" @click="deleteCustomer((item as any).id)">
                                <v-icon>mdi-delete</v-icon>
                            </v-btn>
                        </template>
                    </v-data-table>
                </v-col>
             </v-row>


        </v-container>
    </AppLayout>
</template>