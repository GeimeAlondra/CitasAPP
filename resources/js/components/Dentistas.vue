<template>
  <div class="content">
    <div class="md-layout-item md-medium-size-100 md-xsmall-size-100 md-size-100">
      <v-overlay :value="loader" :z-index="'99999999'">
        <v-progress-circular indeterminate size="80" color="grey darken-4"></v-progress-circular>
      </v-overlay>
      <v-card>
        <v-card-title>
          Listado de dentistas disponibles
          <div class="flex-grow-1"></div>
          <v-text-field v-model="search" label="Buscar dentista" hide-details></v-text-field>
        </v-card-title>
        <v-data-table
          :headers="hTBDentistas"
          :items="arrayDentistas"
          :footer-props="{
            'items-per-page-options': [5,10, 20, 30,40],
            'items-per-page-text' : 'Registros por página'
          }"
          :items-per-page="5"
          :search="search"
          multi-sort
          class="elevation-1"
        >
          <!-- Template Para Modal de Actualizar y Agregar Dentista -->

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
                      <v-form ref="formDentista" v-model="validForm" :lazy-validation="true">
                         <v-text-field
                         append-icon="mdi-folder-outline"
                          v-model="dentista.nombre"
                          @keyup="errorsNombre = []"
                          :rules="[v => !!v || 'Nombre es requerido']"
                          label="Nombre"
                          required
                          :error-messages="errorsNombre"
                        ></v-text-field>
                         <v-textarea                          
                          label="Consultorio" 
                          no-resize
                          rows="2" 
                          append-icon="mdi-folder-outline"
                          v-model="dentista.consultorio" 
                          @keyup="errorsNombre = []"
                          :rules="[v => !!v || 'Consultorio es requerido']"
                          required
                          :error-messages="errorsNombre"                       
                        ></v-textarea>
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
                      @click="saveDentista()"
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
                  @click="deleteDentista(item)"
                >
                  <v-icon>mdi-delete</v-icon>
                </v-btn>
              </template>
              <span>Eliminar dentista</span>
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
      arrayDentistas: [],
      hTBDentistas: [
        { text: "Nombre", value: "nombre" },
        { text: "Consultorio", value: "consultorio" },
        { text: "Acciones", value: "action", sortable: false, align: "center" }
      ],
      loader: false,
      search: "",
      dialog: false,
      dentista: {
        id: null,
        nombre: "",
        consultorio: "",
      },
      validForm: true,
      snackbar: false,
      msjSnackBar: "",
      errorsNombre: [],
      editedDentista: -1
    };
  },
  computed: {
    formTitle() {
      return this.dentista.id === null
        ? "Agregar dentista"
        : "Actualizar dentista";
    },
    btnTitle() {
      return this.dentista.id === null ? "Guardar" : "Actualizar";
    }
  },
  methods: {
    fetchDentistas() {
      let me = this;
      me.loader = true;
        axios.get(`/dentistas/all`)
        .then(function(response) {
          me.arrayDentistas = response.data;
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
        me.dentista = {
          id: null,
          nombre: "",
          consultorio: "",
        };
        me.resetValidation();
      }, 300);
    },
    resetValidation() {
      let me = this;
      me.errorsNombre = [];
      me.$refs.formDentista.resetValidation();
    },
    showModalEditar(dentista) {
      let me = this;
      me.editedDentista = me.arrayDentistas.indexOf(dentista);
      me.dentista = Object.assign({}, dentista);
      me.dialog = true;
    },
    
 saveDentista() {
      let me = this;
      if (me.$refs.formDentista.validate()) {
        let accion = me.dentista.id == null ? "add" : "upd";
        me.loader = true;
        if(accion=="add"){
            axios.post(`/dentistas/save`,me.dentista)
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
                axios.put(`/dentistas/update`,me.dentista)
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
    deleteDentista(dentista) {
      let me = this;
      me.editedDentista = me.arrayDentistas.indexOf(dentista);
      
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
        title: 'Eliminar Dentista',
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
            axios.post(`/dentistas/delete`, dentista)
            .then(function(response) {
                me.verificarAccionDato(response.data, response.status, "del");
                me.loader = false;
            })
          }
        });
    },
     verificarAccionDato(dentista, statusCode, accion) {
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
          //Agrego al array de dentistas el objecto que devuelve el backend
          this.fetchDentistas(); 
          Toast.fire({
            icon: 'success',
            title: 'Dentista registrado con exito'
           });
          me.loader = false;
          break;
        case "upd":
          //Actualizo al array de dentistas el objecto que devuelve el backend ya con los datos actualizados
          this.fetchDentistas(); 
          Toast.fire({
            icon: 'success',
            title: 'Dentista actualizado con exito'
           }); 
            
          me.loader = false;
          break;
        case "del":
          if (statusCode == 200) {
            try {
              //Se elimina del array de dentistas activos si todo esta bien en el backend
              me.arrayDentistas.splice(me.editedDentista, 1);
              Toast.fire({
                icon: 'success',
                title: 'Dentista eliminado'
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
    me.fetchDentistas();
  }
};
</script>