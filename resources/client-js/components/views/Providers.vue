<template>
    <div class="gameCategory">
		<template v-if="!providersLoading">
			<div class="header">
				{{ $t('general.sidebar.providers') }}
			</div>
			<div class="container-fluid" style="max-width: 1420px; margin: 0px auto;">
			   <div class="games">
				  <div v-for="(provider, index) in Providers" class="game_poster_external game-provider game_type-external hvr-float-shadow">
					 <div class="game_poster_external-image game_tp-image" :style="`background: url('${provider.img}') 50% no-repeat !important;`" @click="$router.push(`/game/provider/${provider.name}`)">
						<div class="game_poster_external-provider">
						   {{ provider.name }}
						</div>
					 </div>
				  </div>
			   </div>
			</div>
			<template v-if="!pageLoading">
			<template v-if="(page * depth < count)">
					<div class="divider">
						<div class="line"></div>
						<div class="divider-title">
							<div class="show-more_progress-track">
								<div :style="{ width: ((page * depth / count) * 100) + '%' }" class="show-more_progress-bar"></div>
							</div>
							<div class="show-more_text">Shown <span> {{ page * depth }} </span> from <span>{{ count }}</span> games</div>
							<button @click="pageLoad()" href="javascript:void(0)" class="btn show-more btn-primary"><i class="fas fa-random" aria-hidden="true"></i> Show more</button>				
						</div>
						<div class="line"></div>
					</div>
			</template>
			</template>
			<div v-else class="games-load">
			<loader></loader>
			</div>
		</template>
		<div v-else class="games-load">
			<loader></loader>
		</div>
    </div>
</template>

<script>
    import { mapGetters } from 'vuex';

    export default {
        data() {
            return {
				Providers: [],
				providersLoading: true,
				pageLoading: true,
				page: 0,
				depth: 30,
				count: 0
            }
        },
        computed: {
            ...mapGetters(['isGuest'])
        },
        created() {
			var payload = {
					page: this.page
			};
			axios.post('/api/data/providers', payload).then(({ data }) => {
				this.page += 1;
                this.Providers = data[0].providers;
				this.count = data[0].count;
				if(this.Providers.length === 0) {
					this.$router.push('/404');
					return;
				}
				this.providersLoading = false;
				this.pageLoading = false;
            });
        },
		methods: {
			pageLoad() {
				this.pageLoading = true;
				var payload = {
						page: this.page
				};
				axios.post('/api/data/providers', payload).then(({ data }) => {
					this.page += 1;
					this.Providers = this.Providers.concat(data[0].providers);
					this.count = data[0].count;
					this.providersLoading = false;
					this.pageLoading = false;
				});
			}
        }
    }
</script>

<style lang="scss" scoped>
    @import "resources/sass/variables";

	.games-load {
		display: flex;
		align-items: center;
		justify-content: center;
		margin-top: 30px;
	}
	
	.show-more_progress-track {
		@include themed() {
			background-color: darken(t('secondary'), 20%);
		}
	}
	
	.show-more_progress-bar {
		height: 0.5rem;
		border-radius: 0.5rem;
		@include themed() {
			background-color: t('secondary');
		}
	}
	
	.show-more_text {
		font-size: 0.7rem;
		margin-bottom: 10px;
	}
	
	.show-more_progress-track {
		width: 10rem;
		max-width: 100%;
		height: 0.5rem;
		border-radius: 0.5rem;
		margin-top: 20px;
		margin-bottom: 15px;
	}
	
	.btn.show-more {
		padding: 8px 20px;
	}
	
    .warning {
        width: 100%;
        text-align: center;
        font-size: 1.1em;
        margin-top: 15px;
        margin-bottom: 15px;

     }

    .gameCategory {
        @include themed() {
            .header {
                background: rgba(t('sidebar'), .8);
                backdrop-filter: blur(20px);
                border-bottom: 2px solid t('border');
                margin-top: -15px;
                padding: 35px 45px;
                font-size: 1.8em;
                position: sticky;
                top: 73px;
                z-index: 555;
            }
        }
    }
	
	.game_poster_external-provider {
		bottom: 15px !important;
		text-transform: capitalize;
	}
</style>
