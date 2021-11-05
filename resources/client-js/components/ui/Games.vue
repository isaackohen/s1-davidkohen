<template>
    <div class="container-fluid" style="max-width: 1420px; margin: 0px auto;">
        <template v-for="(cat, key) in categories">
            <div class="index_cat">
                <i class="fab fa-gripfire"></i> {{ $t('general.sidebar.' + key) }}
            </div>

            <div class="games">
                <div v-for="game in cat" :key="game.id" :class="`game_poster_${game.type} game-${game.id} game_type-${game.type} hvr-float-shadow`">
                    <div :class="`game_poster_${game.type}-image game_tp-image`" v-if="game.ext" :style="`background: url('https://games.cdn4.dk/games${game.icon}?q=93&auto=format&fit=crop&sharp=5&w=205&h=137&usm=5') no-repeat !important; background-position-x: center !important;`"  @click="game.ext ? $router.push(`/casino/${game.id}`) : $router.push(`/game/${game.id}`)">
                   
                    <div :class="`game_poster_${game.type}-provider`" v-if="game.ext" @click="game.ext ? $router.push(`/casino/${game.id}`) : $router.push(`/game/${game.id}`)">
                        {{ game.p }}
                    </div>
                    </div>
                    <div :class="`game_poster_${game.type}-image game_tp-image`" v-if="!game.ext" @click="game.ext ? $router.push(`/casino/${game.id}`) : $router.push(`/game/${game.id}`)" :style="`background: url('https://cdn.davidkohen.com/provablyfair/${game.id}.png?q=99&sharp=5&w=205&h=145&fit=crop&usm=5&fm=png') no-repeat !important; background-position-x: center !important;`">

                        <div class="unavailable" v-if="game.d">
                            <div class="slanting">
                                <div class="content">
                                    {{ $t(game.isPlaceholder ? 'general.coming_soon' : 'general.not_available') }}
                                </div>
                            </div>
                        </div>
                    </div>
                    <div :class="`game_poster_${game.type}-houseEdge`" v-if="game.houseEdge" @click="game.ext ? $router.push(`/casino/${game.id}`) : $router.push(`/game/${game.id}`)">
                        {{ game.houseEdge }}% House Edge
                    </div>

                    <div :class="`game_poster_${game.type}-label`" v-if="game.ext"  @click="game.ext ? $router.push(`/casino/${game.id}`) : $router.push(`/game/${game.id}`)">
                        {{ game.name }}
                    </div>

                    <div :class="`game_poster_${game.type}-name`" v-if="!game.ext"  @click="game.ext ? $router.push(`/casino/${game.id}`) : $router.push(`/game/${game.id}`)">
                        {{ game.name }}
                    </div>
                    <div :class="`game_poster_${game.type}-footer`" @click="toggleFavoriteGame(game.id)" v-if="!isGuest">
                        <template v-if="!favMarkLoading">
                            <i :class="`fa${!user.user.favoriteGames || !user.user.favoriteGames.includes(game.id) ? 'l' : 's'} fa-star`"></i>
                            {{ $t(!user.user.favoriteGames || !user.user.favoriteGames.includes(game.id) ? 'general.sidebar.mark_as_favorite' : 'general.sidebar.remove_from_favorite') }}
                        </template>
                        <loader v-else></loader>
                    </div>
                </div>
            </div>
        </template>
    </div>
</template>

<script type="text/javascript">
    import { mapGetters } from 'vuex';

    export default {
        props: ['categories'],
        computed: {
            ...mapGetters(['user', 'isGuest'])
        },
        data() {
            return {
                favMarkLoading: false
            }
        },
        methods: {
            toggleFavoriteGame(id) {
                if(this.favMarkLoading) return;
                this.favMarkLoading = true;
                axios.post('/api/user/markGameAsFavorite', { id: id }).then(() => {
                    this.$store.dispatch('update');
                    this.favMarkLoading = false;
                }).catch(() => this.favMarkLoading = false);
            }
        }
    }
</script>

<style lang="scss">
    @import "resources/sass/variables";

    .index_cat {
        font-size: 1.15em;
        font-weight: 600;
        margin-bottom: 15px;
        margin-top: 25px;
        padding-left: 40px;
        display: flex;
        flex-direction: row;
        align-items: center;

        i {
            margin-right: 5px;
            width: 20px;
            text-align: center;
        }
    }

    .game_poster_local, .game_poster_external, .game_placeholder {
        display: inline-flex;
        flex-direction: column;
        width: 205px;
        min-width: 205px;
        height: 137px;
        border-radius: 7px;
        position: relative;
        box-shadow: 0px 7px 10px rgb(0 0 0 / 50%);

        @include themed() {
            background: rgba(t('sidebar'), .82);
            backdrop-filter: blur(20px);
        }


        &.game_type-local {
            background-size: cover !important;
            background-position: center !important;
            height: 145px !important;
            &-image {
                height: 100% !important;
            }
        }


        &.game_type-external {
            background-size: cover !important;
            background-position: center !important;
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
            transform: scale(1);
            transition: all 0.4s ease;

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
            background: rgba(t('sidebar'), .65);
            backdrop-filter: blur(1px);
            }
            width: 101%;
            opacity: 0;
            transition: opacity .3s ease;
            transition-delay: .1s;
            font-size: 0.8em;
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
            height: 30px;
            z-index: 7;
            border-bottom-left-radius: 7px;
            border-bottom-right-radius: 7px;
            padding: 15px 20px;
            @include themed() {
            background: rgba(t('sidebar'), .65);
            backdrop-filter: blur(2px);
            }
            width: 101%;
            opacity: 0;
            transition: opacity .3s ease;
            transition-delay: .1s;
            font-size: 0.8em;
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
            background: rgba(t('sidebar'), .65);
            backdrop-filter: blur(1px);
            }
            width: 101%;
            opacity: 0;
            transition: opacity .3s ease;
            transition-delay: .1s;
            font-size: 0.8em;
            display: flex;
            align-items: center;

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

        margin-bottom: 10px;
        margin-right: 6px;
        margin-left: 6px;
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

    .game-bullvsbear {
        &-image {
            &:after {
                background-image: url('/img/game/bullvsbear.svg');
                background-color: #3178f4;
            }
        }
    }



    .games {
        display: flex;
        flex-wrap: wrap;
        justify-content: center;
    }

    @include media-breakpoint-down(md) {
        .game_poster_local, .game_poster_external, .game_placeholder {
            width: calc(22%) !important;
			min-width: unset !important;

            &-image {
                height: 100% !important;
            }
        }
    }

    @include media-breakpoint-down(sm) {	
        .game_poster_local, .game_poster_external, .game_placeholder {
            width: calc(40%) !important;
            min-width: unset !important;
            max-width: 205px !important;

            &-image {
                height: 100% !important;
               }
        }

        .image {
            width: 100%;
            background-position: center;
        }

        .slideContent .description {
            width: calc(100% - 15px) !important;
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
        .index_cat {
            padding-left: 0 !important;
        }
        .game_poster_external-label {
            opacity: 1;
        }

        .game_poster_local, .game_poster_external, .game_placeholder {
            margin-right: 8px !important;
            width: 44% !important;
            background-position-y: center !important;
        }
    }
</style>
