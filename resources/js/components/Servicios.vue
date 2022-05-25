<template>
  <div class="content">
    <div class="md-layout-item md-medium-size-100 md-xsmall-size-100 md-size-100">
      <v-overlay :value="loader" :z-index="'99999999'">
        <v-progress-circular indeterminate size="80" color="grey darken-4"></v-progress-circular>
      </v-overlay>
      <v-card>
        <v-card-title>
          Listado de servicios disponibles
          <div class="flex-grow-1"></div>
          <v-text-field v-model="search" label="Buscar servicio" hide-details></v-text-field>
        </v-card-title>
        <v-data-table
          :headers="hTBServicios"
          :items="arrayServicios"
          :footer-props="{
            'items-per-page-options': [5,10, 20, 30,40],
            'items-per-page-text' : 'Registros por página'
          }"
          :items-per-page="5"
          :search="search"
          multi-sort
          class="elevation-1"
        >
          <!-- Template Para Modal de Actualizar y Agregar Servicio -->

          <template v-slot:top>
            <v-toolbar flat color="white">
              <div class="flex-grow-1"></div>
              <v-dialog v-model="dialog" persistent max-width="700px">
                <template v-slot:activator="{ on }">
                  <v-btn elevation="10" color="grey darken-3" dark class="mb-2" v-on="on">
                    Agregar&nbsp;
                    <v-icon>mdi-plus-box-multiple-outline</v-icon>
                  </v-btn>
                </template>
                <v-card>
                  <v-card-title class="headline grey lighten-2" primary-titles>
                    <span class="headline" v-text="formTitle"></span>
                  </v-card-title>
                  <v-card-text>
                    <v-container>
                      <v-form ref="formServicio" v-model="validForm" :lazy-validation="true">
                        <v-text-field
                          append-icon="mdi-folder-outline"
                          v-model="servicio.descripcion"
                          @keyup="errorsNombre = []"
                          :rules="[v => !!v || 'Descripción es requerida']"
                          label="Descripción"
                          required
                          :error-messages="errorsNombre"
                        ></v-text-field>
                        
                      </v-form>
                    </v-container>
                  </v-card-text>
                  <v-divider></v-divider>
                  <v-card-actions>
                    <div class="flex-grow-1"></div>
                    <v-btn color="red darken-1" text @click="cerrarModal">Cerrar</v-btn>
                    <v-btn
                      color="info darken-1"
                      :disabled="!validForm"
                      @click="saveServicio()"
                      text
                      v-text="btnTitle"
                    ></v-btn>
                  </v-card-actions>
                </v-card>
              </v-dialog>
            </v-toolbar>
          </template>
         
          <!-- Template que va en la tabla en la columna de Acciones (Editar,Desactivar) -->
        
          <template v-slot:item.action="{item}" v-slot:activator="{ on }">
            <v-tooltip top>
              <template v-slot:activator="{ on }">
                <v-btn
                  color="success"
                  elevation="8"
                  small
                  dark
                  :disabled="item.id < 0"
                  v-on="on"
                  @click="showModalEditar(item)"
                >
                  <v-icon>mdi-pencil</v-icon>
                </v-btn>
              </template>
              <span>Actualizar datos</span>
            </v-tooltip>
            <v-tooltip top >
              <template v-slot:activator="{ on }" >
                <v-btn
                  color="info"
                  class="mx-1"
                  elevation="8"
                  small
                  dark
                  :disabled="item.id < 0"
                  v-on="on"
                  @click="deleteServicio(item)"
                >
                  <v-icon>mdi-delete</v-icon>
                </v-btn>
              </template>
              <span>Eliminar servicio</span>
            </v-tooltip>
          </template>
        </v-data-table>
        <v-snackbar v-model="snackbar">
          {{ msjSnackBar }}
          <v-btn color="red" text @click="snackbar = false">Cerrar</v-btn>
        </v-snackbar>
      </v-card>
    </div>
  </div>
</template>
<script>
export default {
  data() {
     return {
      arrayServicios: [],
      hTBServicios: [
        { text: "Descripción", value: "descripcion" },
        { text: "Acciones", value: "action", sortable: false, align: "center" }
      ],
      loader: false,
      search: "",
      dialog: false,
      servicio: {
        id: null,
        descripcion: ""
      },
      validForm: true,
      snackbar: false,
      msjSnackBar: "",
      errorsNombre: [],
      editedServicio: -1
    };
  },
  computed: {
    formTitle() {
      return this.servicio.id === null
        ? "Agregar servicio"
        : "Actualizar servicio";
    },
    btnTitle() {
      return this.servicio.id === null ? "Guardar" : "Actualizar";
    }
  },
  methods: {
    
    fetchServicios() {
      let me = this;
      me.loader = true;
      axios
      .get(`/servicios/all`)
        .then(function(response) {
          me.arrayServicios = response.data;
          me.loader = false;
        })
        .catch(function(error) {
          me.loader = false;
          console.log(error);
        });
     me.loader = false;
    },
    
    setMessageToSnackBar(msj, estado) {
      let me = this;
      me.snackbar = estado;
      me.msjSnackBar = msj;
    },
    cerrarModal() {
      let me = this;
      me.dialog = false;
      setTimeout(() => {
        me.servicio = {
          id: null,
          descripcion: ""
        };
        me.resetValidation();
      }, 300);
    },
    resetValidation() {
      let me = this;
      me.errorsNombre = [];
      me.$refs.formServicio.resetValidation();
    },
    showModalEditar(servicio) {
      let me = this;
      me.editedServicio = me.arrayServicios.indexOf(servicio);
      me.servicio = Object.assign({}, servicio);
      me.dialog = true;
    },
    saveServicio() {
      let me = this;
      if (me.$refs.formServicio.validate()) {
        let accion = me.servicio.id == null ? "add" : "upd";
        me.loader = true;
        if(accion=="add"){
            axios.post(`/servicios/save`,me.servicio)
            .then(function(response) {
             console.log(response.status);
              if(response.status ==201){
                 me.verificarAccionDato(response.data, response.status, accion);
                 me.cerrarModal(); 
                 console.log(response.status);
              }else{
                Vue.swal("Error", "Ocurrio un error, intente de nuevo", "error");
                me.cerrarModal();
              }
            
            })
            .catch(function(error){
               Vue.swal("Error", "Ocurrio un error, intente de nuevo", "error");
            });
            me.loader = false;
        }else{
            //Para actualizar
                axios.put(`/servicios/update`,me.servicio)
               .then(function(response) {
                 if(response.status==202){
                    me.verificarAccionDato(response.data, response.status, accion);
                        me.cerrarModal();  
                 }else{
                    Vue.swal("Error", "Ocurrio un error, intente de nuevo", "error");
                     me.cerrarModal();
                 }
              })
          .catch(function(error) {
            console.log(error);
            me.loader = false;
          });
        }
      }
    },
    deleteServicio(servicio) {
      let me = this;
      me.editedServicio = me.arrayServicios.indexOf(servicio);
      
      const Toast = Vue.swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true,
        onOpen: (toast) => {
            toast.addEventListener('mouseenter', Swal.stopTimer)
            toast.addEventListener('mouseleave', Swal.resumeTimer)
        }
        });
        //Personalizando nueva confirmacion
        Vue.swal.fire({
        title: 'Eliminar Servicio',
        text: "Una vez realizada la acción no se podra revertir",
        type: 'question',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Si',
        cancelButtonText: "No"
        }).then((result) => {
        if (result.value) {
            me.loader = true;
            axios.post(`/servicios/delete`, servicio)
            .then(function(response) {
                me.verificarAccionDato(response.data, response.status, "del");
                me.loader = false;
            })
          }
        });
    },
     verificarAccionDato(servicio, statusCode, accion) {
      let me = this;
      
      const Toast = Vue.swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true,
        onOpen: (toast) => {
            toast.addEventListener('mouseenter', Swal.stopTimer)
            toast.addEventListener('mouseleave', Swal.resumeTimer)
        }
        }); 
      switch (accion) {
        case "add":
          //Agrego al array de servicios el objecto que devuelve el backend
          this.fetchServicios(); 
          Toast.fire({
            icon: 'success',
            title: 'Servicio registrado con exito'
           });
          me.loader = false;
          break;
        case "upd":
          //Actualizo al array de servicios el objecto que devuelve el backend ya con los datos actualizados
          this.fetchServicios(); 
          Toast.fire({
            icon: 'success',
            title: 'Servicio actualizado con exito'
           }); 
            
          me.loader = false;
          break;
        case "del":
          if (statusCode == 200) {
            try {
              //Se elimina del array de servicios activos si todo esta bien en el backend
              me.arrayServicios.splice(me.editedServicio, 1);
              Toast.fire({
                icon: 'success',
                title: 'Servicio eliminado'
              });
            } catch (error) {
              console.log(error);
            }
          } else {
             Toast.fire({
                icon: 'error',
                title: 'Ocurrió un error, intente de nuevo'
              });
          }
          break;
      }
    }
  },
  mounted() {
    let me = this;
    me.fetchServicios();
  }
};
</script>