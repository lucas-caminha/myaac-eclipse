<?php

namespace MyAAC\Plugins\LuaMonsters;

use MyAAC\Plugins\LuaMonsters\Models\LuaMonster as LuaMonsterModel;

class Monsters
{
	public static function reload(bool $show = false): bool
	{
		self::clearDatabase();
		$dataPack = configLua('dataPackDirectory') ?: 'data-otservbr-global';
		$folder = rtrim(config('server_path'), '/\\') . '/' . trim($dataPack, '/\\') . '/monster';
		if (!is_dir($folder)) {
			error('A pasta de monstros não foi encontrada: ' . htmlspecialchars($folder));
			return false;
		}

		$count = self::loadFromLua($folder);
		if ($show) {
			success($count . ' monstros carregados.');
		}
		return true;
	}

	public static function clearDatabase(): void
	{
		LuaMonsterModel::query()->delete();
	}

	public static function loadFromLua(string $folder): int
	{
		set_time_limit(180);
		$count = 0;
		$iterator = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($folder));
		foreach ($iterator as $file) {
			if (!$file->isFile() || strtolower($file->getExtension()) !== 'lua' || str_contains($file->getFilename(), '_functions')) {
				continue;
			}
			$content = file_get_contents($file->getPathname());
			if (!preg_match('/Game\.createMonsterType\(["\'](.+?)["\']\)/', $content, $nameMatch)) {
				continue;
			}

			$name = $nameMatch[1];
			$outfit = self::parseNumericBlock($content, 'outfit');
			$flags = self::parseScalarBlock($content, 'flags');
			$defenses = self::parseScalarBlock($content, 'defenses');
			$speed = self::number($content, 'speed');
			$loot = self::parseLoot($content);

			try {
				LuaMonsterModel::create([
					'name' => $name,
					'mana' => self::number($content, 'manaCost'),
					'exp' => self::number($content, 'experience'),
					'health' => self::number($content, 'health'),
					'bestiary_class' => self::bestiaryClass($content),
					'outfit' => json_encode($outfit),
					'speed_lvl' => $speed <= 220 ? 1 : (int)(($speed - 220) / 2),
					'use_haste' => str_contains($content, 'name = "speed"') ? 1 : 0,
					'summonable' => !empty($flags['summonable']) ? 1 : 0,
					'convinceable' => !empty($flags['convinceable']) ? 1 : 0,
					'rewardboss' => !empty($flags['rewardBoss']) ? 1 : 0,
					'voices' => json_encode(self::parseVoices($content)),
					'immunities' => json_encode(self::parseImmunities($content)),
					'elements' => json_encode(self::parseElements($content)),
					'flags' => json_encode($flags),
					'defense' => (int)($defenses['defense'] ?? 0),
					'armor' => (int)($defenses['armor'] ?? 0),
					'race' => self::text($content, 'race'),
					'summons' => json_encode(self::parseSummons($content)),
					'loot' => json_encode($loot),
					'hide' => 0,
				]);
				$count++;
			} catch (\Throwable $exception) {
				// Um arquivo incompatível não deve interromper a importação completa.
			}
		}
		return $count;
	}

	private static function number(string $content, string $key): int
	{
		return preg_match('/monster\.' . preg_quote($key, '/') . '\s*=\s*(-?\d+)/', $content, $match) ? (int)$match[1] : 0;
	}

	private static function text(string $content, string $key): string
	{
		return preg_match('/monster\.' . preg_quote($key, '/') . '\s*=\s*["\']([^"\']*)["\']/', $content, $match) ? $match[1] : '';
	}

	private static function bestiaryClass(string $content): string
	{
		$block = self::block($content, 'Bestiary');
		return preg_match('/\bclass\s*=\s*["\']([^"\']+)["\']/', $block, $match) ? trim($match[1]) : '';
	}

	private static function block(string $content, string $key): string
	{
		return preg_match('/monster\.' . preg_quote($key, '/') . '\s*=\s*\{(.*?)\n\}/s', $content, $match) ? $match[1] : '';
	}

	private static function parseNumericBlock(string $content, string $key): array
	{
		$values = [];
		preg_match_all('/(\w+)\s*=\s*(-?\d+)/', self::block($content, $key), $matches, PREG_SET_ORDER);
		foreach ($matches as $match) $values[$match[1]] = (int)$match[2];
		return $values;
	}

	private static function parseScalarBlock(string $content, string $key): array
	{
		$values = [];
		preg_match_all('/(\w+)\s*=\s*(true|false|-?\d+)/', self::block($content, $key), $matches, PREG_SET_ORDER);
		foreach ($matches as $match) $values[$match[1]] = $match[2] === 'true' ? true : ($match[2] === 'false' ? false : (int)$match[2]);
		return $values;
	}

	private static function parseVoices(string $content): array
	{
		preg_match_all('/\{\s*text\s*=\s*["\']([^"\']+)["\']/', self::block($content, 'voices'), $matches);
		return $matches[1] ?? [];
	}

	private static function parseImmunities(string $content): array
	{
		preg_match_all('/type\s*=\s*["\']([^"\']+)["\']\s*,\s*condition\s*=\s*true/', self::block($content, 'immunities'), $matches);
		return $matches[1] ?? [];
	}

	private static function parseElements(string $content): array
	{
		$map = ['PHYSICAL' => 'Físico', 'ENERGY' => 'Energia', 'EARTH' => 'Terra', 'FIRE' => 'Fogo', 'LIFEDRAIN' => 'Dreno de vida', 'MANADRAIN' => 'Dreno de mana', 'DROWN' => 'Afogamento', 'ICE' => 'Gelo', 'HOLY' => 'Sagrado', 'DEATH' => 'Morte'];
		$result = [];
		preg_match_all('/type\s*=\s*COMBAT_(\w+?)(?:DAMAGE)?\s*,\s*percent\s*=\s*(-?\d+)/', self::block($content, 'elements'), $matches, PREG_SET_ORDER);
		foreach ($matches as $match) $result[] = ['name' => $map[$match[1]] ?? ucfirst(strtolower($match[1])), 'percent' => (int)$match[2]];
		return $result;
	}

	private static function parseLoot(string $content): array
	{
		$result = [];
		preg_match_all('/\{\s*(?:id\s*=\s*(\d+)\s*,\s*)?name\s*=\s*["\']([^"\']+)["\']\s*,\s*chance\s*=\s*(\d+)(?:\s*,\s*maxCount\s*=\s*(\d+))?/', self::block($content, 'loot'), $matches, PREG_SET_ORDER);
		foreach ($matches as $match) $result[] = ['id' => (int)($match[1] ?? 0), 'name' => $match[2], 'chance' => (int)$match[3], 'maxCount' => (int)($match[4] ?? 1)];
		return $result;
	}

	private static function parseSummons(string $content): array
	{
		$result = [];
		preg_match_all('/name\s*=\s*["\']([^"\']+)["\'].*?chance\s*=\s*(\d+)/', self::block($content, 'summons'), $matches, PREG_SET_ORDER);
		foreach ($matches as $match) $result[] = ['name' => $match[1], 'chance' => (int)$match[2]];
		return $result;
	}
}
