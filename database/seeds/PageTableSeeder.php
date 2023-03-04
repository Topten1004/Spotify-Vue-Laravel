<?php

use Illuminate\Database\Seeder;

use App\Page;

class PageTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Page::truncate();
        // // seeding the default Pages
        Page::create([
            'name' => 'charts',
            'title' => 'Charts',
            'blank' => 0,
            'description' => '',
            'icon' => '',
            'path' => '/browse/charts',
        ]);
        Page::create([
            'name' => 'Home',
            'title' => 'Explore',
            'blank' => 0,
            'description' => 'Explore & enjoy listening to music',
            'icon' => 'home',
            'path' => '/home',
        ]);
        Page::create([
            'name' => 'About Us',
            'title' => 'About Us',
            'description' => 'Read more about us',
            'path' => '/about-us',
            'blank' => 1,
            'content' => '
            <h1>About Us Sample</h1>
            <br/>
            <p>You should add your own "About us" description here or disable this page.</p>
            <div class="boxed"><div id="lipsum">
            <p>
            Lorem ipsum dolor sit amet, consectetur adipiscing elit. Suspendisse ut auctor dolor. Pellentesque fringilla lacus sit amet massa euismod pulvinar. Praesent ac erat nibh. Maecenas at imperdiet est, at fringilla ante. Phasellus fermentum nisi vitae faucibus laoreet. Nulla sit amet posuere metus. Donec quis neque a augue tempus vulputate at non urna. Aenean porta metus sit amet tellus mattis sodales. Suspendisse ac nulla massa. Curabitur auctor porta laoreet. Nullam vel quam lorem. Aenean ornare a purus eget dictum.
            </p>
            <p>
            Morbi egestas convallis sem quis sollicitudin. Fusce vitae purus tortor. Vestibulum finibus orci purus, quis sodales nunc pulvinar sed. Orci varius natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. In luctus felis a ex sagittis congue. Nunc vel bibendum ligula. Nulla a vehicula urna. Cras suscipit pretium ipsum, eu feugiat risus tincidunt at. Pellentesque imperdiet sem id risus facilisis, nec aliquet libero aliquet. Mauris ultrices interdum ante quis auctor. Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos. Quisque vitae malesuada ante. Nam euismod felis commodo nulla lobortis, eu fermentum magna varius. Sed ut mauris lobortis, dapibus ipsum quis, iaculis libero. Mauris blandit, turpis vel convallis faucibus, erat massa luctus massa, at viverra magna odio non tellus.
            </p>
            <p>
            Aliquam nibh elit, ornare vitae elementum id, vulputate a neque. Duis porta placerat tincidunt. Ut convallis lectus ligula, ut imperdiet mauris sagittis sit amet. Fusce sit amet faucibus enim. Cras rutrum nisi eget porta lobortis. Ut quam elit, finibus sit amet neque ac, dapibus fringilla urna. Duis ultrices dapibus sem, nec suscipit metus vehicula at. Pellentesque facilisis purus nec pretium tincidunt. Nam aliquet nibh quis nisl dignissim fermentum. Curabitur eget ipsum ultricies, sollicitudin ex sit amet, rhoncus nisl. Nunc tincidunt purus pretium, suscipit nulla sit amet, congue nibh. Aenean tempus aliquet ante, imperdiet luctus nunc sagittis vel. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nulla ut eros auctor, placerat nisl vitae, fringilla diam. Duis tempor diam ut felis sagittis porttitor. Suspendisse consectetur ipsum ipsum, vulputate suscipit odio volutpat a.
            </p>
            <p>
            Donec a sapien purus. Vivamus a diam eros. Sed pellentesque facilisis lacinia. Donec molestie, ante ac sodales eleifend, purus nisi hendrerit sem, nec pellentesque purus odio eget purus. Pellentesque pharetra libero et enim faucibus, non ultricies elit facilisis. Proin commodo lacinia odio in lobortis. Phasellus cursus quam ut libero aliquam tincidunt. Suspendisse eu lacus blandit, tempor nisl vel, mollis mi. Curabitur lacus ipsum, ullamcorper blandit vehicula at, aliquet vitae ligula. Mauris accumsan nunc a nisi scelerisque, vitae efficitur erat egestas. Sed gravida ac ex a egestas.
            </p>
            <p>
            In tellus velit, scelerisque mollis malesuada quis, lobortis ut purus. Suspendisse potenti. Vestibulum at venenatis nulla. Mauris in elit a sapien venenatis imperdiet eget vel augue. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nulla vitae diam vitae augue bibendum bibendum et quis ligula. Duis facilisis aliquet justo vel pellentesque. Mauris ut fermentum nisi. Donec tincidunt felis ut enim ultrices accumsan.
            </p></div>
            '
        ]);

        Page::create([
            'name' => 'Privacy Policy',
            'title' => 'Privacy policy',
            'description' => 'Read about our privacy policy',
            'path' => '/privacy-policy',
            'blank' => 1,
            'content' => '
            <h1>Privacy policy Page Sample</h1>
            <br />
            <p>You should add your own "Privacy Policy" description here or disable this page.</p>
            <div class="boxed"><div id="lipsum">
            <p>
            Lorem ipsum dolor sit amet, consectetur adipiscing elit. Suspendisse ut auctor dolor. Pellentesque fringilla lacus sit amet massa euismod pulvinar. Praesent ac erat nibh. Maecenas at imperdiet est, at fringilla ante. Phasellus fermentum nisi vitae faucibus laoreet. Nulla sit amet posuere metus. Donec quis neque a augue tempus vulputate at non urna. Aenean porta metus sit amet tellus mattis sodales. Suspendisse ac nulla massa. Curabitur auctor porta laoreet. Nullam vel quam lorem. Aenean ornare a purus eget dictum.
            </p>
            <p>
            Morbi egestas convallis sem quis sollicitudin. Fusce vitae purus tortor. Vestibulum finibus orci purus, quis sodales nunc pulvinar sed. Orci varius natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. In luctus felis a ex sagittis congue. Nunc vel bibendum ligula. Nulla a vehicula urna. Cras suscipit pretium ipsum, eu feugiat risus tincidunt at. Pellentesque imperdiet sem id risus facilisis, nec aliquet libero aliquet. Mauris ultrices interdum ante quis auctor. Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos. Quisque vitae malesuada ante. Nam euismod felis commodo nulla lobortis, eu fermentum magna varius. Sed ut mauris lobortis, dapibus ipsum quis, iaculis libero. Mauris blandit, turpis vel convallis faucibus, erat massa luctus massa, at viverra magna odio non tellus.
            </p>
            <p>
            Aliquam nibh elit, ornare vitae elementum id, vulputate a neque. Duis porta placerat tincidunt. Ut convallis lectus ligula, ut imperdiet mauris sagittis sit amet. Fusce sit amet faucibus enim. Cras rutrum nisi eget porta lobortis. Ut quam elit, finibus sit amet neque ac, dapibus fringilla urna. Duis ultrices dapibus sem, nec suscipit metus vehicula at. Pellentesque facilisis purus nec pretium tincidunt. Nam aliquet nibh quis nisl dignissim fermentum. Curabitur eget ipsum ultricies, sollicitudin ex sit amet, rhoncus nisl. Nunc tincidunt purus pretium, suscipit nulla sit amet, congue nibh. Aenean tempus aliquet ante, imperdiet luctus nunc sagittis vel. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nulla ut eros auctor, placerat nisl vitae, fringilla diam. Duis tempor diam ut felis sagittis porttitor. Suspendisse consectetur ipsum ipsum, vulputate suscipit odio volutpat a.
            </p>
            <p>
            Donec a sapien purus. Vivamus a diam eros. Sed pellentesque facilisis lacinia. Donec molestie, ante ac sodales eleifend, purus nisi hendrerit sem, nec pellentesque purus odio eget purus. Pellentesque pharetra libero et enim faucibus, non ultricies elit facilisis. Proin commodo lacinia odio in lobortis. Phasellus cursus quam ut libero aliquam tincidunt. Suspendisse eu lacus blandit, tempor nisl vel, mollis mi. Curabitur lacus ipsum, ullamcorper blandit vehicula at, aliquet vitae ligula. Mauris accumsan nunc a nisi scelerisque, vitae efficitur erat egestas. Sed gravida ac ex a egestas.
            </p>
            <p>
            In tellus velit, scelerisque mollis malesuada quis, lobortis ut purus. Suspendisse potenti. Vestibulum at venenatis nulla. Mauris in elit a sapien venenatis imperdiet eget vel augue. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nulla vitae diam vitae augue bibendum bibendum et quis ligula. Duis facilisis aliquet justo vel pellentesque. Mauris ut fermentum nisi. Donec tincidunt felis ut enim ultrices accumsan.
            </p></div>
            '
        ]);

        Page::create([
            'name' => 'Terms of Service',
            'title' => 'Terms of Service',
            'description' => 'Terms of Service page description',
            'path' => '/terms-of-service',
            'blank' => 1,
            'content' => '
            <h1>Terms of Service Page Sample</h1>
            <br />
            <p>You should add your own "Terms of Service" description here or disable this page.</p>
            <div class="boxed"><div id="lipsum">
            <p>
            Lorem ipsum dolor sit amet, consectetur adipiscing elit. Suspendisse ut auctor dolor. Pellentesque fringilla lacus sit amet massa euismod pulvinar. Praesent ac erat nibh. Maecenas at imperdiet est, at fringilla ante. Phasellus fermentum nisi vitae faucibus laoreet. Nulla sit amet posuere metus. Donec quis neque a augue tempus vulputate at non urna. Aenean porta metus sit amet tellus mattis sodales. Suspendisse ac nulla massa. Curabitur auctor porta laoreet. Nullam vel quam lorem. Aenean ornare a purus eget dictum.
            </p>
            <p>
            Morbi egestas convallis sem quis sollicitudin. Fusce vitae purus tortor. Vestibulum finibus orci purus, quis sodales nunc pulvinar sed. Orci varius natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. In luctus felis a ex sagittis congue. Nunc vel bibendum ligula. Nulla a vehicula urna. Cras suscipit pretium ipsum, eu feugiat risus tincidunt at. Pellentesque imperdiet sem id risus facilisis, nec aliquet libero aliquet. Mauris ultrices interdum ante quis auctor. Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos. Quisque vitae malesuada ante. Nam euismod felis commodo nulla lobortis, eu fermentum magna varius. Sed ut mauris lobortis, dapibus ipsum quis, iaculis libero. Mauris blandit, turpis vel convallis faucibus, erat massa luctus massa, at viverra magna odio non tellus.
            </p>
            <p>
            Aliquam nibh elit, ornare vitae elementum id, vulputate a neque. Duis porta placerat tincidunt. Ut convallis lectus ligula, ut imperdiet mauris sagittis sit amet. Fusce sit amet faucibus enim. Cras rutrum nisi eget porta lobortis. Ut quam elit, finibus sit amet neque ac, dapibus fringilla urna. Duis ultrices dapibus sem, nec suscipit metus vehicula at. Pellentesque facilisis purus nec pretium tincidunt. Nam aliquet nibh quis nisl dignissim fermentum. Curabitur eget ipsum ultricies, sollicitudin ex sit amet, rhoncus nisl. Nunc tincidunt purus pretium, suscipit nulla sit amet, congue nibh. Aenean tempus aliquet ante, imperdiet luctus nunc sagittis vel. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nulla ut eros auctor, placerat nisl vitae, fringilla diam. Duis tempor diam ut felis sagittis porttitor. Suspendisse consectetur ipsum ipsum, vulputate suscipit odio volutpat a.
            </p>
            <p>
            Donec a sapien purus. Vivamus a diam eros. Sed pellentesque facilisis lacinia. Donec molestie, ante ac sodales eleifend, purus nisi hendrerit sem, nec pellentesque purus odio eget purus. Pellentesque pharetra libero et enim faucibus, non ultricies elit facilisis. Proin commodo lacinia odio in lobortis. Phasellus cursus quam ut libero aliquam tincidunt. Suspendisse eu lacus blandit, tempor nisl vel, mollis mi. Curabitur lacus ipsum, ullamcorper blandit vehicula at, aliquet vitae ligula. Mauris accumsan nunc a nisi scelerisque, vitae efficitur erat egestas. Sed gravida ac ex a egestas.
            </p>
            <p>
            In tellus velit, scelerisque mollis malesuada quis, lobortis ut purus. Suspendisse potenti. Vestibulum at venenatis nulla. Mauris in elit a sapien venenatis imperdiet eget vel augue. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nulla vitae diam vitae augue bibendum bibendum et quis ligula. Duis facilisis aliquet justo vel pellentesque. Mauris ut fermentum nisi. Donec tincidunt felis ut enim ultrices accumsan.
            </p></div>
            '
        ]);
    }
}
