<template>
	<div class="container-fluid">
		<div v-if="isGuest" class="container-lg">
			<p>Please login to play this game.</p>
		</div>
		<div v-if="!isGuest" class="container-lg">
			<template v-if="!Loading">
				<div :class="`game-container game-${$route.params.id}`">
					<div id="slotcontainer">
						<div class="gameWrapper">
							 <iframe :src="externalgame.url"  frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen="allowfullscreen"></iframe>
						</div>
					</div>
					<div class="gameFooter">
						<b>{{ externalgame.name }}</b>
						<small>by <u style="cursor: pointer;" @click="$router.push(`/game/provider/${externalgame.provider}`)">{{ externalgame.provider }}</u></small>
						<div class="right">
							<div class="form-check pl-0">
                        		<label class="form-check-label">{{ $t('general.games.toggleDemoMode') }}</label>
                        		<toggle-button class="toggleDemoMode" :value="RealMode" @change="toggleDemoMode" :labels="{checked: 'real', unchecked: 'demo'}"/>
                    		</div>
						</div>
					</div>
				</div> 
				<div class="container-fluid game-container-slick">
					<div class="games-arrows" id="c1-arrows">
					  <div class="games-arrow" @click="showPrev"><i class="fas fa-arrow-circle-left"></i></div>
					  <div class="games-arrow" @click="showNext"><i class="fas fa-arrow-circle-right"></i></div>
					</div>
					<VueSlickCarousel ref="casinoCarousel" v-bind="carouselSettings">
						<div v-for="footerGame in footerGames">
							<div :class="`game_poster_${footerGame.type} game-${footerGame.id} game_type-${footerGame.type} hvr-grow`">
								<div :class="`game_poster_${footerGame.type}-image game_tp-image`" v-if="footerGame.ext" :style="`background: url('https://cdn.davidkohen.com/games${footerGame.icon}?q=85&auto=format&sharp=5&w=205&h=137&fit=crop&usm=5') no-repeat !important; background-size: cover !important;`"  @click="footerGame.ext ? $router.push(`/casino/${footerGame.id}`) : $router.push(`/game/${footerGame.id}`)">
									<div :class="`game_poster_${footerGame.type}-name`" v-if="!footerGame.ext"  @click="footerGame.ext ? $router.push(`/casino/${footerGame.id}`) : $router.push(`/game/${footerGame.id}`)">
										{{ footerGame.name }}
									</div>
								</div>
							</div>
						</div>
					</VueSlickCarousel>
				</div>
			</template>
			<div v-else class="games-load">
				<loader></loader>
			</div>
		</div>
	</div>
</template>

<script>
    import { mapGetters } from 'vuex';
	import VueSlickCarousel from 'vue-slick-carousel';
    export default {
        data() {
            return {
            	RealMode: null,
				Loading: true,
                carouselSettings: {
                  "dots": false,
                  "arrows": false,
                  "infinite": false,
                  "speed": 500,
                  "slidesToShow": 5,
                  "slidesToScroll": 1,
                  "cssEase": 'cubic-bezier(0.175, 0.885, 0.320, 1.275)',
                  "responsive": [
                    {
                      "breakpoint": 1250,
                      "settings": {
                        "slidesToShow": 4,
                        "slidesToScroll": 2
                      }
                    },
                    {
                      "breakpoint": 1050,
                      "settings": {
                        "slidesToShow": 3,
                        "slidesToScroll": 1
                      }
                    },
                    {
                      "breakpoint": 480,
                      "settings": {
                        "slidesToShow": 1,
                        "slidesToScroll": 1
                      }
                    }
                  ]
                },
                footerGames: [],
                externalgame: null
            }
        },
        methods: {
            showNext() {
                this.$refs.casinoCarousel.next()
            },
            showPrev() {
                this.$refs.casinoCarousel.prev()
            },
            toggleDemoMode() {
	            axios.post('/api/externalGame/getUrl', { id: this.$route.params.id, mode: this.RealMode }).then(({ data }) => {
	                this.externalgame = data;
	            	this.RealMode = externalgame.mode;
	           });
            },
        },
        computed: {
            ...mapGetters(['isGuest', 'gameInstance'])
        },
        components: { VueSlickCarousel },
        created() {
            axios.post('/api/externalGame/getUrl', { id: this.$route.params.id, mode: this.RealMode }).then(({ data }) => {
                this.externalgame = data;
				this.$store.dispatch('pushRecentGame', data.id);
				this.RealMode = true;
				this.Loading = false;
           });
            axios.post('/api/externalGame/getGamesbyProvider', { id: this.$route.params.id }).then(({ data }) => {
                this.footerGames = data;
            });
        }

    }
</script>

<style lang="scss">
	@import "resources/sass/variables";
		
		
	.games-load {
		display: flex;
		align-items: center;
		justify-content: center;
		margin-top: 30px;
	}

	.game-type-third-party {
		display: flex;
		flex-direction: column;
		position: relative;
		min-height: 550px
	}

	.games-arrow {
		opacity: 0.7;
		cursor: pointer;
		margin-left: 4px;
		transition: opacity 0.3s ease;

		&:hover {
			opacity: 1;
		}
	}
	.games-arrows {
			display: flex;
	}

	.game-container-slick {
		margin-top: 20px;
		margin-bottom: 50px;
	}

	.game-type-third-party iframe {
		 position: absolute;
		 top: 0;
		 border-top-left-radius: 8px;
		 border-top-right-radius: 8px;
		 left: 0;
		 width: 100%;
		 height: 100%;
	}

	#slotcontainer {
		outline: none !important;
		border-bottom-right-radius: 0px;
		border-bottom-left-radius: 0px;
		margin-top: 25px;
		max-width: 1500px;
		border-top-left-radius: 7px;
		border-top-right-radius: 7px;
		padding-left: 0px !important;
		padding-right: 0px !important;
		background: #00000091;
	}
	.gameWrapper {
		position: relative;
		padding-bottom: 56.25%;
		height: 0;
		border-top-left-radius: 7px;
		border-top-right-radius: 7px;
	}

	.gameWrapper iframe {
		position: absolute;
		top: 0;
		border-top-left-radius: 7px;
		border-top-right-radius: 7px;
		left: 0;
		width: 100%;
		height: 100%;
	}
	.gameFooter {
		padding: 15px 5px 15px 25px; 
		@include themed() {
			background: t('sidebar');
		}
	}


	 @include media-breakpoint-down(sm) {
	 
		 .game-container-slick {
			display: none;
		}
			
		 .gameWrapper {
			 position: relative;
			 padding-bottom: 1px !important;
			 height: 100%;
			 border-top-left-radius: 7px;
			 border-top-right-radius: 7px;
			 border: none !important;
		}
		 .gameWrapper iframe {
			 position: relative;
			 top: 0;
			 border-top-left-radius: 7px;
			 border-top-right-radius: 7px;
			 left: 0;
			 width: 100%;
			 height: 100%;
			 min-height: 80vh;
		}
		 #slotcontainer {
			 background: #00000091;
			 padding-right: 0px;
			 margin-top: 25px;
			 max-width: 1500px;
			 border-top-left-radius: 0px;
			 border-top-right-radius: 0px;
			 padding-left: 0px !important;
			 padding-right: 0px !important;
		}
	}

		.game_poster_local, .game_poster_external, .game_placeholder {
			display: inline-flex;
			flex-direction: column;
			width: 205px;
			min-width: 205px;
			border-radius: 7px;
			position: relative;

			@include themed() {
				background: rgba(t('sidebar'), .82);
				backdrop-filter: blur(20px);
			}


			&.game_type-local {
			height: 137px !important;

				&-image {
					height: 100% !important;
				}
			}


			&.game_type-external {
			height: 137px !important;

				&-image {
					height: 100% !important;
				}
			}

			&-image {
				width: 100%;
				height: 100%;
				border-radius: 5px;
				position: relative;
				z-index: 5;
				background-position: center;
				background-size: cover;
				background-repeat: no-repeat;

				.game_tp-image {
					position: absolute;
					left: 0;
					top: 0;
					width: 100%;
					height: 100%;
					background-size: cover;
					background-position: center;
					background-repeat: no-repeat;
					z-index: 1;
				}


			}

			&-provider {
				font-size: 0.82em;
				position: absolute;
				bottom: 55px;
				right: 0;

				@include themed() {
					background: linear-gradient(to right, transparent 0%, t('sidebar') 170%);
				}

				padding: 10px 25px;
				padding-right: 10px;
			}


			&-houseEdge {
				font-size: 0.8em;
				position: absolute;
				bottom: 55px;
				right: 0;

				@include themed() {
					background: linear-gradient(to right, transparent 0%, t('sidebar') 170%);
				}

				padding: 10px 25px;
				padding-right: 10px;
			}

			&-name {
				position: absolute;
				bottom: -1px;
				left: -1px;
				height: 45px;
				z-index: 7;
				border-bottom-left-radius: 7px;
				border-bottom-right-radius: 7px;
				padding: 10px 20px;
				@include themed() {
				background: rgba(t('sidebar'), .7);
				backdrop-filter: blur(1px);
				}
				width: 101%;
				opacity: 0;
				transition: opacity .3s ease;
				transition-delay: .1s;
				font-size: 0.9em;
				display: flex;
				align-items: center;

				i {
					margin-right: 5px;
				}
			}


			&-label {
				position: absolute;
				bottom: -1px;
				left: -1px;
				height: 45px;
				z-index: 7;
				border-bottom-left-radius: 7px;
				border-bottom-right-radius: 7px;
				padding: 15px 20px;
				@include themed() {
				background: rgba(t('sidebar'), .7);
				backdrop-filter: blur(1px);
				}
				width: 101%;
				opacity: 0;
				transition: opacity .3s ease;
				transition-delay: .1s;
				font-size: 0.9em;
				display: flex;
				align-items: center;

				i {
					margin-right: 5px;
				}
			}

			&-footer {
				position: absolute;
				top: 0;
				left: -1px;
				height: 25px;
				border-top-left-radius: 6px;
				border-top-right-radius: 6px;
				z-index: 7;
				padding: 15px 20px;
				@include themed() {
				background: rgba(t('sidebar'), .5);
				backdrop-filter: blur(1px);
				}
				width: 101%;
				opacity: 0;
				transition: opacity .3s ease;
				transition-delay: .1s;
				font-size: 0.8em;
				display: flex;
				align-items: center;

		        .form-check {
		            height: 40px;
		            display: flex;
		            align-items: center;

		            .profileToggle {
		                margin: auto;
		                margin-right: 0;
		            }
		        }

				.loaderContainer {
					transform: scale(.2) translate(-200%);
				}

				i {
					margin-right: 5px;
				}
			}

			@include only_safari('.game_poster-name', (
				font-weight: unset
			));

			&-houseEdge {
				z-index: 6;
			}

			.vue-content-placeholders-img {
				height: 100% !important;
			}

			margin: 11px;
			z-index: 1;
			transition: all 0.3s ease;

			&:hover {
				z-index: 5;

				.game_poster_local-footer, .game_poster_external-footer, .game_poster_local-name {
					opacity: 1;

					&-provider {
								@include themed() {
									background: linear-gradient(to right, transparent 0%, t('sidebar') 70%);
								}
					}
				}
				.game_poster_local-label, .game_poster_external-label, .game_poster_local-name {
					opacity: 1;
				}
			}

			cursor: pointer;
			border-radius: 5px;
		}

		.game_poster {
			.unavailable {
				z-index: 4;
				position: absolute;
				top: 0;
				left: 0;
				width: 100%;
				height: 100%;
				background: rgba(black, 0.4);
				color: white;

				.slanting {
					transform: skewY(-5deg) translateY(-50%);
					padding: 25px;
					position: absolute;
					top: 50%;
					background: rgba(black, 0.85);
					width: 100%;

					.content {
						font-size: 15px;
						transform: skewY(5deg);
						text-align: center;
					}
				}
			}
		}

		.games {
			display: flex;
			flex-wrap: wrap;
			justify-content: center;
		}

</style>
