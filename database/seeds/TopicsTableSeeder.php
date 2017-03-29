<?php

use App\Models\Topic;
use Faker\Generator;
use Illuminate\Database\Seeder;

class TopicsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @param Generator $faker
     * @return void
     */
    public function run(Generator $faker)
    {
        /**
         * 「根话题」
         * 描述
        知乎的全部话题通过父子关系构成一个有根无循环的有向图。
        「根话题」即为所有话题的最上层的父话题。
        话题精华即为知乎的 Top1000 高票回答。
        请不要在问题上直接绑定「根话题」。
        这样会使问题话题过于宽泛。修改
         *
         */
        $root = Topic::create([
            'name' => '「根话题」',
            'description' => '知乎的全部话题通过父子关系构成一个有根无循环的有向图。「根话题」即为所有话题的最上层的父话题。话题精华即为知乎的 Top1000 高票回答。请不要在问题上直接绑定「根话题」。 这样会使问题话题过于宽泛。修改',
            'parent_id' => 0,
            'logo' =>   $faker->imageUrl($width = 50, $height = 50),
            'followers_count' => 0,
            'questions_count' => 0,
        ]);

        /**
         * 「未归类」话题
         * 描述
            知乎的全部话题通过父子关系构成一个有根无循环的有向图。
            所有没有直接添加父话题的话题会自动成为「未归类」话题的子话题，从而与整个话题树连接起来。
         * 未归类话题属于跟话题
         */
        $noCat = Topic::create([
            'name' => '「未归类」话题',
            'description' => '知乎的全部话题通过父子关系构成一个有根无循环的有向图。所有没有直接添加父话题的话题会自动成为「未归类」话题的子话题，从而与整个话题树连接起来。未归类话题属于跟话题',
            'parent_id' => $root->id,
            'logo' =>   $faker->imageUrl($width = 50, $height = 50),
            'followers_count' => 0,
            'questions_count' => 0,
        ]);

        /*
         * 「形而上」话题
         * 描述
            「形而上」话题下收录了一些讨论概念、逻辑、含义和原因等抽象内容的子话题。
            「形而上」是日本人井上哲次郎对 metaphysic 一词的汉字翻译，语出《易经》中「形而上者谓之道，形而下者谓之器」
            请不要在问题上直接绑定「形而上」话题
            属于根话题
         */
        $xes = Topic::create([
            'name' => '「形而上」话题',
            'description' => '「形而上」话题下收录了一些讨论概念、逻辑、含义和原因等抽象内容的子话题。「形而上」是日本人井上哲次郎对 metaphysic 一词的汉字翻译，语出《易经》中「形而上者谓之道，形而下者谓之器」请不要在问题上直接绑定「形而上」话题属于根话题',
            'parent_id' => $root->id,
            'logo' =>   $faker->imageUrl($width = 50, $height = 50),
            'followers_count' => 0,
            'questions_count' => 0,
        ]);

        $network = Topic::create([
            'name' => '互联网',
            'description' => '互联网（英语：internet），是网络 与网络之间所串连成的庞大网络，这些网络以一组标准的网络TCP/IP协议族 相连，链接全世界几十亿个设备，形成逻辑上的单一巨大国际网络。这是一个网络的网络，它是由从地方到全球范围内几百万个私人的，学术界的，企业的和政府的网络所构成，通过电子，无线和光纤网络技术等等一系列广泛的技术联系在一起。这种将计算机网络互相联接在一起的方法可称作“网络互联”，在这基础上发展出覆盖全世界的全球性互联网络称互联网，即是互相连接一起的网络。',
            'parent_id' => $xes->id,
            'logo' =>   $faker->imageUrl($width = 50, $height = 50),
            'followers_count' => 0,
            'questions_count' => 0,
        ]);

        $netguo = Topic::create([
            'name' => '互联国',
            'description' => '互联国（英语：internet），是网络 与网络之间所串连成的庞大网络，这些网络以一组标准的网络TCP/IP协议族 相连，链接全世界几十亿个设备，形成逻辑上的单一巨大国际网络。这是一个网络的网络，它是由从地方到全球范围内几百万个私人的，学术界的，企业的和政府的网络所构成，通过电子，无线和光纤网络技术等等一系列广泛的技术联系在一起。这种将计算机网络互相联接在一起的方法可称作“网络互联”，在这基础上发展出覆盖全世界的全球性互联网络称互联网，即是互相连接一起的网络。',
            'parent_id' => $xes->id,
            'logo' =>   $faker->imageUrl($width = 50, $height = 50),
            'followers_count' => 0,
            'questions_count' => 0,
        ]);

    }
}
