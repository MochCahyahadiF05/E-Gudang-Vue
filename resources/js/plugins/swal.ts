import Swal from "sweetalert2";
import { App } from "vue";

export default {
    install: (app: App) => {
        app.config.globalProperties.$swal = Swal;

        app.config.globalProperties.$notify = {
            toast: (message: string, icon: "success" | "error" | "warning") => {
                Swal.fire({
                    toast: true,
                    position: "top-end",
                    icon: icon,
                    title: message,
                    showConfirmButton: false,
                    timer: 3000,
                    timerProgressBar: true,
                });
            },
            confirm: async (message = "Yakin ingin menghapus?") => {
                return Swal.fire({
                    title: message,
                    text: "Tindakan ini tidak bisa dibatalkan!",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#d33",
                    cancelButtonColor: "#3085d6",
                    confirmButtonText: "Ya, hapus!",
                    cancelButtonText: "Batal",
                }).then((result) => result.isConfirmed);
            },
        };
    },
};
