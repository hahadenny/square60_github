<search inline-template >
    <div class="search-box">
        <form id="searchForm" name="searchForm" method="get" v-bind:action="action">
            <div>
                <input type="search" v-model="searchValue" v-on:keyup="autoComplete" name="search" class="search-input input" autocomplete="off" placeholder="address, building name, agent" required />
                <button type="submit" value="" class="cursor">
                    <img src="/images/ico-search.svg" alt="">
                </button>
                <div class="dropdown dropdown-autocomplete" v-bind:class="{'is-active': isActive}" v-if="data_results.length">
                    <ul class="dropdown-content dropdown-menu">
                        <li class="dropdown-item" v-for="result in data_results"><a v-bind:href="result.link">@{{ result.title }}</a></li>
                    </ul>
                </div>
            </div>

            <div class="dropdown dropdown-search">
                <div class="dropdown-trigger">
                    <button class="button" type="button" aria-haspopup="true" aria-controls="dropdown-menu"></button>
                </div>
                <div class="dropdown-menu select-menu" id="dropdown-menu" role="menu">
                    <div class="dropdown-content">
                        <a v-for="type in types" v-bind:key="type.id" v-bind:class="{ 'is-active': type.id == searchType }" v-on:click="changeAction(type)" v-html="type.title" class="dropdown-item"></a>
                    </div>
                </div>
            </div>
        </form>
    </div>
</search>