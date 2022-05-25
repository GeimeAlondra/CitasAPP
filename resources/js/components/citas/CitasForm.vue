<template>
  <div class="content">
    <div
      class="md-layout-item md-medium-size-100 md-xsmall-size-100 md-size-100"
    >
      <v-overlay :value="loader" :z-index="'99999999'">
        <v-progress-circular
          indeterminate
          size="80"
          color="grey darken-4"
        ></v-progress-circular>
      </v-overlay>
      <v-card>
        <v-card-title> Registro de reserva de citas</v-card-title>
         <v-container fluid>
          <v-form
            ref="formCita"
            v-model="validForm"
            :lazy-validation="true"
          >
            <v-row>
              <v-col cols="12" md="2">Fecha de cita</v-col>
              <v-col cols="12" md="3">
                <date-picker
                  v-model="citaForm.fecha"
                  :editable="false"
                  lang="es"
                  value-type="format"
                  format="YYYY-MM-DD"
                  class="mt-3"
                  style="width: 100% !important"
                  input-class="form-control"
                  placeholder="Fecha de cita"
                ></date-picker>
              </v-col>
            </v-row>
          <v-row justify="space-around" align="center">
              <v-col style="width: 350px; flex: 0 1 auto">
                 <v-autocomplete
                  v-model="citaForm.horario"
                  :items="arrayHorarios"
                  :rules="[(v) => !!v || 'Horario es requerido']"
                  required
                  label="Seleccione horario"
                  item-value="id"
                  item-text="hora"
                  :disabled="action == 'detail'"
                  return-object
                ></v-autocomplete>
              </v-col>
              <v-col style="width: 350px; flex: 0 1 auto">
                <v-autocomplete
                  v-model="citaForm.dentista"
                  :items="arrayDentistas"
                  :rules="[(v) => !!v || 'Dentista es requerido']"
                  required
                  label="Seleccione dentista"
                  item-value="id"
                  item-text="nombre"
                  :disabled="action == 'detail'"
                  return-object
                ></v-autocomplete>
              </v-col>
            </v-row>

            <v-row>
              <v-col cols="12" md="12" lg="12" sm="12">
                <v-col cols="12" md="12">
                  <v-alert
                    :value="noServicios"
                    type="error"
                    border="left"
                    transition="scale-transition"
                    dismissible
                    elevation="2"
                    >Debe agregar uno o mas servicios a la reserva</v-alert
                  >
                </v-col>
              </v-col>
              <v-col cols="12" md="12" lg="12" sm="12">
                <v-card outlined>
                  <v-card-title class="mb-4 font-weight-medium subtitle-1">
                    Detalle de la cita
                    <div class="flex-grow-1"></div>
                    <v-text-field
                      v-model="searchInDetail"
                      label="Buscar en detalle"
                      hide-details
                    ></v-text-field>
                  </v-card-title>
                  <v-card-text>
                    <v-data-table
                      :headers="headerTable"
                      :items="citaForm.servicios"
                      no-data-text="No hay servicio agregados"
                      :items-per-page="10"
                      :search="searchInDetail"
                      :footer-props="{
                        'items-per-page-options': [10, 20, 30],
                        showFirstLastPage: true,
                      }"
                    >
                      <template v-slot:top>
                        <v-toolbar flat color="white">
                          <div class="flex-grow-1"></div>
                          <!-- INICIO MODAL-->
                          <v-dialog
                            v-model="modalServicios"
                            persistent
                            max-width="1150px"
                            scrollable
                          >
                            <template v-slot:activator="{ on }">
                              <v-btn
                                elevation="10"
                                color="grey darken-3"
                                dark
                                v-if="action != 'detail'"
                                class="mb-2"
                                v-on="on"
                              >
                                Agregar servicio&nbsp;
                                <v-icon>library_add</v-icon>
                              </v-btn>
                            </template>
                            <v-card>
                              <v-card-title
                                class="headline grey lighten-2"
                                primary-titles
                              >
                                <span
                                  class="headline"
                                  v-text="formTitle"
                                ></span>
                              </v-card-title>
                              <v-card-text>
                                <ServicioList
                                  v-on:created="getItemsOfServiciosOfCita()"
                                  @added="onAddedItem"
                                  ref="servicio"
                                />
                              </v-card-text>
                              <v-divider></v-divider>
                              <v-card-actions>
                                <div class="flex-grow-1"></div>
                                <v-btn
                                  color="red darken-1"
                                  text
                                  @click="modalServicios = false"
                                  >Cerrar</v-btn
                                >
                                <v-btn
                                  color="info darken-1"
                                  @click="getServicioFromChild()"
                                  text
                                  v-text="'Agregar a cita'"
                                ></v-btn>
                              </v-card-actions>
                            </v-card>
                          </v-dialog>
                        </v-toolbar>
                      </template>
                      <!-- FIN MODAL-->

                      <template v-slot:item="props">
                        <tr :key="props.item.id">
                          <td v-text="props.item.servicio.descripcion" />

                          <td class="text-center">
                            <v-btn
                              color="red"
                              class="mx-1"
                              elevation="8"
                              v-if="action != 'detail'"
                              small
                              dark
                              @click="deleteServicioFromCita(props.item)"
                            >
                              <v-icon>delete</v-icon>
                            </v-btn>
                          </td>
                        </tr>
                      </template>

                      <template v-slot:body.append>
                        <tr class="grey lighten-5">
                          <td colspan="5">
                            <v-row v-if="action != 'detail'">
                              <v-col
                                cols="12"
                                class="d-flex flex-row-reverse"
                                md="12"
                              >
                                <v-btn
                                  :disabled="!validForm"
                                  @click="saveOrUpdate()"
                                  large
                                  color="primary"
                                >
                                  {{
                                    action == "add"
                                      ? "Guardar Reserva"
                                      : action == "upd"
                                      ? "Actualizar Reserva"
                                      : ""
                                  }}&nbsp;
                                  <v-icon>save</v-icon>
                                </v-btn>
                              </v-col>
                            </v-row>
                          </td>
                        </tr>
                      </template>
                    </v-data-table>
                  </v-card-text>
                </v-card>
              </v-col>
            </v-row>
          </v-form> 
        </v-container>
      </v-card>
    </div>
  </div>
</template>
<script>
import ServicioList from './ServicioList.vue';

export default {
  name: "CitasForm",
  props: ['user'],
  data() {
    return {
      search: "",
      loader: false,
      fecha: null,
      action: "add",
      citaForm: {
        id: null,
        user: null,
        fecha: new Date(
          Date.now() - new Date().getTimezoneOffset() * 60000
        )
          .toISOString()
          .substr(0, 10),
        estado: "R",
        hora: null,
        dentista: null,
        servicios: [],
      },
      arrayHorarios: [],
      arrayDentistas: [],

      action: "add",
      headerTable: [
        { text: "Descripción", value: "servicio.descripcion", align: "center" },
      ],
      modalServicios: false,
      validForm: true,
      noServicios: false,
      accion: "",
      searchInDetail: "",
      formatted: "",
      loader: false,
      formTitle: "Agregar Servicios",
      date: new Date(Date.now() - new Date().getTimezoneOffset() * 60000)
        .toISOString()
        .substr(0, 10),
    };
  },
  computed: {},
  methods: {
    fetchHorarios() {
      let me = this;
      me.loader = true;
      axios.get(`/horarios/all`)
        .then(function(response) {
          console.log(response.data);
          me.arrayHorarios = response.data;
          me.loader = false;
        })
        .catch(function(error) {
          me.loader = false;
          console.log(error);
        });
     me.loader = false;
    },
     fetchDentistas() {
      let me = this;
      me.loader = true;
      axios.get(`/dentistas/all`)
        .then(function (response) {
          console.log(response.data);
          me.arrayDentistas = response.data;
          me.loader = false;
        })
        .catch(function (error) {
          me.loader = false;
          console.log(error);
        });
        me.loader = false;
    },
    onAddedItem(value) {
      let me = this;
      if (value.length > 0) {
        me.formTitle = "Agregar servicios";
      }
    },
    getItemsOfServiciosOfCita() {
      let me = this,
        servicios = [];
      me.citaForm.servicios.map((detalle) => servicios.push(detalle.servicio));
      me.$refs.servicio.item = servicios;
    },
    getServicioFromChild() {
      let me = this;
      me.$refs.servicio.item.forEach(function (servicio) {
        if (!me.containsObject(servicio, me.citaForm.servicios)) {
          me.citaForm.servicios.push({
            servicio: servicio,
          });
        }
      });
      me.modalServicios = false;
    },
    containsObject(obj, list) {
      return list.some(
        (item) => JSON.stringify(item.servicio) === JSON.stringify(obj)
      );
    },
    //Quitar un item del detalle
    deleteServicioFromCita(item) {
      let me = this,
        index;
      index = me.citaForm.servicios.indexOf(item);
      me.citaForm.servicios.splice(index, 1);
    },
    //Guardar la reserva
    saveOrUpdate() {
      let me = this;
      if (me.$refs.formCita.validate()) {
        if (me.citaForm.servicios.length == 0) {
          me.noServicios = true;
          me.validForm = false;
        } else {
          me.citaForm.user = this.user;
          me.loader = true;
          axios.post(`/citas/save`, me.citaForm)
            .then(function (response) {
              if (response.status == 201) {
                Vue.swal("Ok", "Reserva registrada con exito", "success");
                
              } else {
                Vue.swal(
                  "Error",
                  "Error al registrar la reserva, intente de nuevo",
                  "error"
                );
              }
              me.loader = false;
            })
            .catch(function (error) {
                Vue.swal("Error", "Error al registrar la reserva, intente de nuevo", "error");
            });
        }
      }
    },
  },
  components: {
    ServicioList,
  },
  mounted() {
    let me = this;
    console.log(this.user);
    me.fetchHorarios();
    me.fetchDentistas();
  },
};
</script>
<style scope>
.centered-input >>> input {
  text-align: center;
}
</style>