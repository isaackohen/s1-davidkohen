<template>
    <div class="gameCategory">
        <div class="header">
           <span>Home</span>
        </div>
        <template v-if="!gamesLoading">
			<games :categories="categoryGames"></games>
		</template>
		<div v-else class="games-load">
			<loader></loader>
		</div>
    </div> 
</template>

<script>
    import Bus from '../../bus';
    import { mapGetters } from 'vuex';
    import FaucetModal from "../modals/FaucetModal";
    import AuthModal from "../modals/AuthModal";
    import PasswordResetModal from "../modals/PasswordResetModal";

    export default {
        computed: {
            ...mapGetters(['isGuest'])
        },
        data() {
            return {
				gamesData: {},
                categoryGames: {},
				gamesLoading: true
            };
        },
        watch: {
            gamesData() {
                this.categoryGames = {};
                this.load();
            }
        },
        methods: {
            openFaucetModal() {
                FaucetModal.methods.open();
            },
            openAuthModal(type) {
                AuthModal.methods.open(type);
            },
            load() {
                let duplicates = [];
                _.forEach(this.gamesData, (game) => {
					if(game.cat === 'slots') return;
					if(game.cat === 'rollback') return;
					if(game.cat === 'featured') return;
					if(game.cat === 'cardgames') return;
					if(game.cat === 'bonus') return;
					if(game.cat === 'vs') return;
					if(game.cat === 'scratchcards') return;
					if(game.cat === 'new') return;

					if (duplicates.includes(game.id)) return;
					duplicates.push(game.id);

					if (!this.categoryGames[game.cat]) this.categoryGames[game.cat] = [game];
					else this.categoryGames[game.cat].push(game);
                });
            }
        },
        created() {
		    var payload = {
				category: [
					'inhouse'
				],
				subcategory: [
					'popular'
				]
			};
			axios.post('/api/data/games', payload).then(({ data }) => {
                this.gamesData = data[0].games;
				this.gamesLoading = false;
            });
            this.load();
            if(this.$route.params.user && this.$route.params.token)
                PasswordResetModal.methods.open(this.$route.params.user, this.$route.params.token);
        }
    }
</script>

<style lang="scss">
    @import "resources/sass/variables";

    .gameCategory {
        @include themed() {
            .header {
                background: rgba(t('sidebar'), .8);
                backdrop-filter: blur(20px);
                border-bottom: 2px solid t('border');
                margin-top: -15px;
                padding: 35px 45px;
                font-size: 1.5em;
                position: static;
                top: 73px;
                z-index: 555;
            }
        }
    }
	.games-load {
		display: flex;
		align-items: center;
		justify-content: center;
		margin-top: 30px;
	}

    .glide__slide--active {
        opacity: 1 !important;
        transition: opacity 0.3s ease;
    }
    .slider {
        min-height: 140px;
        height: 9vmax;
        max-height: 150px;
        display: flex;
        margin-bottom: 15px;
        margin-top: -18px;
        margin-left: -15px;
        width: calc(100% + 30px) !important;
        transition: opacity 0.3s ease;
        
        #slider {
            width: 0;
            flex: 1;
        }

        .glide {
            height: 100%;

            .glide__track {
                height: 100%;
            }

            .glide__slides {
                height: 100%;
            }

            .glide__arrow--left, .glide__arrow--right {
                padding: 0 !important;
                border: none !important;
                border-radius: 0 !important;
                box-shadow: none !important;
                height: 35px;
                width: 15px;
                opacity: 0.5 !important;
                transition: opacity 0.3s ease;
                &:hover {
                    opacity: 1 !important;
                }
            }

            .glide__arrow--right {
                background: url("data:image/svg+xml;charset=utf-8,%3Csvg width='14' height='37' fill='none' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M13.738 18.663l-12.448 18L0 34.8l11.176-16.136L0 2.527 1.29.663l12.448 18z' fill='%23fff'/%3E%3C/svg%3E");
            }

            .glide__arrow--left {
                background: url("data:image/svg+xml;charset=utf-8,%3Csvg width='16' height='36' fill='none' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M.994 18l12.928 18 1.34-1.864L3.655 18 15.262 1.864 13.922 0 .994 18z' fill='%23fff'/%3E%3C/svg%3E");
            }

            .glide__slide {
                width: 100%;
                height: 100% !important;
                position: relative;
                opacity: 0.92;
                background-size: cover !important;

                @include themed() {
                    background-color: rgba(t('sidebar'), .8);
                    backdrop-filter: blur(20px);
                    border-bottom: 2px solid t('border');
                }

                background-position: center;
                background-repeat: no-repeat;

                &:hover {
                    opacity: 1;
                }
                &:active {
                    opacity: 1;
                }

                .image {
                    background-size: cover;
                    background-repeat: no-repeat;
                    height: 90px;
                    width: 190px;
                    z-index: 1;
                    filter: drop-shadow(1px 1px 2px rgba(0, 0, 0, .2));
                }

                .slideContent {
                    width: 70%;
                    display: flex;
                    margin-left: auto;
                    margin-right: auto;
                    height: 100%;
                    flex-direction: column;
                    color: white;
                    position: relative;
                    z-index: 2;

                    &.right {
                        align-items: flex-end;
                        text-align: right;

                        .slideContentWrapper {
                            align-items: flex-end;
                        }
                    }

                    .slideContentWrapper {
                        display: flex;
                        flex-direction: column;
                        margin-top: auto;
                        margin-bottom: auto;
                    }

                    .header, .description {
                        text-shadow: 1px 1px 3px rgba(black, 0.2);
                    }

                    .header {
                        font-weight: 600;
                        font-size: 2em;
                        margin-bottom: 25px;
                    }

                    .description {
                        font-size: 1.5em;
                        margin-bottom: 25px;
                        width: 320px;
                    }

                    .button {
                        width: 150px;
                        text-align: center;
                        padding: 10px 25px;
                        background: rgba(white, 0.1);
                        border: 1px solid rgba(white, 0.15);
                        cursor: pointer;
                        transition: background 0.3s ease, border-color 0.3s ease;

                        &:hover {
                            background: rgba(white, 0.2);
                            border-color: rgba(white, 0.25);
                        }
                    }
                }
            }
        }
    }

    @include media-breakpoint-up(lg) {
        .slider {
            width: 100%;
        }
    }

    @include media-breakpoint-down(md) {
        .slider {
            margin-left: -15px;
            width: calc(100% + 30px) !important;
            height: 150px !important;
            min-height: 150px !important;
            max-height: 150px !important;
            .image {
                height: 45px !important;
                width: 70px !important;
            }

            .button {
                width: 100px !important;
                font-size: 0.8em !important;
                padding: 5px !important;
            }

            .glide__arrow--left {
                left: 2em !important;
            }

            .glide__slide {
                background-size: contain !important;
            }

            .glide__slide {
                border-radius: 0;
            }

            .slideContentWrapper {
                .header {
                    font-size: 1.6em !important;
                }

                .description {
                    font-size: 1em !important;
                }
            }
        }
    }

    @include media-breakpoint-down(sm) {
        .image {
            width: 100%;
            background-position: center;
        }

        .slideContent .description {
            width: calc(100% - 95px) !important;
            margin-bottom: 15px !important;
        }

        .slideContent .header {
            margin-bottom: 15px !important;
        }

        .glide__bullets, .glide__arrows {
            display: none;
        }
    }

        @media(max-width: 450px) {

    }
</style>
