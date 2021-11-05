<template>
	<div>
		<div class="row">
			<div class="col-12">
				<div class="page-title-box">
					<h4 class="page-title">External Settings</h4>
				</div>
			</div>
		</div>
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <table v-if="externalGames" id="datatable" class="table dt-responsive nowrap">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Category</th>
									<th>Provider</th>
									<th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-if="externalGames" v-for="game in externalGames" style="cursor: pointer" :key="game._id">
                                    <td><img alt :src="`https://cdn.davidkohen.com/games` + game.image" style="width: 32px; height: 32px; margin-right: 5px;"> {{ game.name }}</td>
                                    <td>{{ game.category }}</td>
									<td>{{ game.provider }}</td>
									<td>
										<div @click="toggleExtGame(game._id)" class="form-switch mt-1">
											<input type="checkbox" name="color-scheme-mode" value="light" id="light-mode-check" :checked="!game.isDisabled" class="form-check-input"> 
										</div>
									</td>
                                </tr>
                                <div v-else>
									<div class="text-center mt-2"><div class="spinner-border text-primary m-2" role="status">
																<span class="visually-hidden">Loading...</span>
															</div></div>
														</div>
                            </tbody>
                        </table>
						<div v-else>
							<div class="text-center mt-2"><div class="spinner-border text-primary m-2" role="status">
								<span class="visually-hidden">Loading...</span>
							</div></div>
						</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import { mapGetters } from 'vuex';

    export default {
        created() {
		  axios.post('/admin/extgames/games').then(({data}) => {
                this.externalGames = data;

                setTimeout(() => {
                    $('#datatable').DataTable({
                        destroy: true,
                        "language": {
                            "paginate": {
                                "previous": "< ",
                                "next": " >"
                            }
                        },
                        "drawCallback": function () {
                            $('.dataTables_paginate > .pagination').addClass('pagination-rounded');
                        }
                    });
                }, 1000);
            });
        },
        data() {
            return {
                externalGames: null
            }
        },
        computed: {
            ...mapGetters(['games'])
        },
        methods: {
			toggleExtGame(id) {
                axios.post('/admin/extToggle', { id: id });
            }
        }
    }
</script>
