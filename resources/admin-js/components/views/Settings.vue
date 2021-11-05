<template>
    <div>
		<div class="row">
			<div class="col-12">
				<div class="page-title-box">
				</div>
			</div>
		</div>
		
		<div class="row">
			<div class="col-12">
				<div class="card">
					<div class="card-body">
						<div class="row align-items-center">
							<div class="col-xl-2 col-lg-3 col-6">
								<img src="/img/misc/cal.png" class="me-4 align-self-center img-fluid" alt="cal">
							</div>
							<div class="col-xl-10 col-lg-9">
								<div class="mt-4 mt-lg-0">
									<h5 class="mt-0 mb-1 fw-bold">Settings list</h5>
									<p class="text-muted mb-2">
										These options are related to the platform settings, 
										please make sure that the platform is in maintenance mode before changing system/default settings or platform.
									</p>
									<button class="btn btn-primary mt-2 me-1" id="btn-new-event" data-bs-toggle="modal" data-bs-target="#event-modal">
										Create New Setting
									</button>
								</div>
							</div>
						</div>
						
					</div> <!-- end card body-->
				</div> <!-- end card -->
			</div>
			<!-- end col-12 -->
		</div>
		<div class="row page-title" v-if="settingsMutable.length > 0">
            <div class="col-md-12">
				<h3 class="mb-1 mt-0">General Settings</h3>
				<h6>The general settings for your platform and external services.</h6>
				<hr> 
            </div>
        </div>
        <div class="row">
            <div class="col-xl-3 col-lg-6" v-for="setting in settingsMutable">
                <div class="card">
                    <div class="card-body">
                        <h5><a @click="clipboard(setting.name)" href="javascript:void(0)" class="text-muted" data-toggle="tooltip" data-placement="top" title="Copy">{{ setting.name }}</a></h5>
                        <div class="text-muted">
                            {{ setting.description }}
                            <div class="form-group mt-2">
                                <input @input="change(setting.name, $event.target.value)" :value="setting.value" type="text" class="form-control" placeholder="Value">
                            </div>
                        </div>
                    </div>
                    <div class="card-body border-top">
                        <div class="row align-items-center">
                            <div class="col-sm-auto">
                                <ul class="list-inline mb-0">
                                    <li class="list-inline-item pr-2">
                                        <a @click="remove(setting.name)" href="javascript:void(0)" class="text-muted d-inline-block">
                                            Remove
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
		<div class="row page-title" v-if="settingsGlobal.length > 0">
            <div class="col-md-12">
				<h3 class="mb-1 mt-0">Api Settings</h3>
				<h6>The Api settings of platform. [<code>Read-only</code>]</h6>
				<hr> 
            </div>
        </div>
        <div class="row">
            <div class="col-xl-3 col-lg-6" v-for="setting in settingsGlobal">
                <div class="card">
                    <div class="card-body">
                        <h5><a @click="clipboard(setting.name)" href="javascript:void(0)" class="text-muted" data-toggle="tooltip" data-placement="top" title="Copy">{{ setting.name }}</a></h5>
                        <div class="text-muted">
                            {{ setting.description }}
                            <div class="form-group mt-2">
                                <input readonly :value="setting.value" type="password" data-toggle="tooltip" data-placement="top" :title="setting.value" class="form-control" placeholder="Value">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
		<div class="row page-title" v-if="settingsBonus.length > 0">
            <div class="col-md-12">
				<h3 class="mb-1 mt-0">Bonus Settings</h3>
				<h6>The bonus settings for your platform and external services.</h6>
				<hr> 
            </div>
        </div>
        <div class="row">
            <div class="col-xl-3 col-lg-6" v-for="setting in settingsBonus">
                <div class="card">
                    <div class="card-body">
                        <h5><a @click="clipboard(setting.name)" href="javascript:void(0)" class="text-muted" data-toggle="tooltip" data-placement="top" title="Copy">{{ setting.name }}</a></h5>
                        <div class="text-muted">
                            {{ setting.description }}
                            <div class="form-group mt-2">
                                <input readonly :value="setting.value" type="text" class="form-control" placeholder="Value">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row page-title" v-if="settingsImmutable.length > 0">
            <div class="col-md-12">
				<h3 class="mb-1 mt-0">System Settings</h3>
				<h6>The system settings of platform. [<code>Read-only</code>]</h6>
				<hr> 
            </div>
        </div>
        <div class="row">
            <div class="col-xl-3 col-lg-6" v-for="setting in settingsImmutable">
                <div class="card">
                    <div class="card-body">
                        <h5><a @click="clipboard(setting.name)" href="javascript:void(0)" class="text-muted" data-toggle="tooltip" data-placement="top" title="Copy">{{ setting.name }}</a></h5>
                        <div class="text-muted">
                            {{ setting.description }}
                            <div class="form-group mt-2">
                                <input readonly :value="setting.value" type="text" class="form-control" placeholder="Value">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
		<div class="modal fade" id="event-modal" tabindex="-1" style="display: none;" aria-hidden="true">
			<div class="modal-dialog">
				<div class="modal-content">
					<div class="modal-header py-3 px-4 border-bottom-0 d-block">
						<button type="button" class="btn-close float-end" data-bs-dismiss="modal" aria-hidden="true"></button>
						<h5 class="modal-title" id="modal-title">New Key</h5>
					</div>
					<div class="modal-body px-4 pb-4">
						<form class="needs-validation was-validated" name="event-form" id="form-event" novalidate="">
							<div class="row">
								<div class="col-12">
									<div class="mb-3">
										<label class="form-label">Key</label>
										<input class="form-control" placeholder="Key" type="text" id="key" v-model="newSettingKey">
									</div>
								</div>
								<div class="col-12">
									<div class="mb-3">
										<label class="form-label">Category</label>
										<select class="form-control" id="cat" v-model="newSettingCat">
												<option value="">None</option>
												<option value="general">General Settings</option>
												<option value="apigamble">APIGamble Settings</option>
												<option value="social">Social Settings</option>
												<option value="jackpot">Jackpot Settings</option>
												<option value="bonus">Bonus Settings</option>
												<option value="race">Race Settings</option>
												<option value="custom">Client Specific Custom Setting</option>
												<option value="system">System Variable</option>
										</select>
										<div class="invalid-feedback">Please select a valid event category</div>
									</div>
								</div>
							</div>
							<div class="row mt-2">
								<div class="col-6">
									<button type="button" class="btn btn-danger" id="btn-delete-event" style="display: none;">Delete</button>
								</div>
								<div class="col-6 text-end">
									<button type="button" class="btn btn-light me-1" data-bs-dismiss="modal">Close</button>
									<button type="submit" class="btn btn-success" id="btn-save-event" @click="create">Save</button>
								</div>
							</div>
						</form>
					</div>
				</div> <!-- end modal-content-->
			</div> <!-- end modal dialog-->
		</div>
    </div>
</template>

<script>
    export default {
        created() {
            axios.post('/admin/settings/get').then(({ data }) => {
                this.settingsMutable = data.mutable;
                this.settingsImmutable = data.immutable;
				this.settingsBonus = data.bonus;
				this.settingsGlobal = data.global;
            });
        },
        data() {
            return {
                settingsMutable: [],
                settingsImmutable: [],
				settingsBonus: [],
				settingsGlobal: [],

                newSettingKey: '',
                newSettingDescription: '',
				newSettingCat: ''
            }
        },
        methods: {
            change(key, value) {
                axios.post('/admin/settings/edit', { key: key, value: value.length === 0 ? 'null' : value });
            },
            remove(key) {
                axios.post('/admin/settings/remove', { key: key }).then(() => this.$router.go());
            },
			clipboard(text) {
				navigator.clipboard.writeText(text);
			},
            create() {
                axios.post('/admin/settings/create', { key: this.newSettingKey, description: this.newSettingDescription, cat: this.newSettingCat }).then(() => window.location.reload());
            }
        }
    }
</script>
